<?php

namespace Claudsonm\CepPromise\Providers;

use Claudsonm\CepPromise\Contracts\BaseProvider;
use Claudsonm\CepPromise\Exceptions\CepPromiseProviderException;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class CepAbertoProvider extends BaseProvider
{
    const CEP_ABERTO_TOKEN = '37d718d2984e6452584a76d3d59d3a26';

    /**
     * O nome identificador do provedor de serviço.
     *
     * @var string
     */
    public $providerIdentifier = 'cep_aberto';

    /**
     * Cria a Promise para obter os dados de um CEP no provedor do serviço.
     *
     * @param string $cep
     *
     * @return \GuzzleHttp\Promise\Promise
     */
    public function makePromise(string $cep)
    {
        $url = "http://www.cepaberto.com/api/v2/ceps.json?cep=$cep";
        $httpVerb = 'GET';
        $options = [
            'headers' => [
                'Authorization' => 'Token token='.self::CEP_ABERTO_TOKEN,
                'Content-Type' => 'application/json;charset=utf-8',
            ],
        ];
        $this->promise = $this->client->requestAsync($httpVerb, $url, $options)
            ->then(call_user_func([__CLASS__, 'analyzeAndParseResponse']))
            ->then(call_user_func([__CLASS__, 'checkForViaCepError']))
            ->then(call_user_func([__CLASS__, 'extractCepValuesFromResponse']))
            ->otherwise(call_user_func([__CLASS__, 'throwApplicationError']));

        return $this->promise;
    }

    private function analyzeAndParseResponse()
    {
        return function (ResponseInterface $response) {
            $content = $response->getBody()->getContents();

            return json_decode($content, true);
        };
    }

    private function checkForViaCepError()
    {
        return function (array $responseArray) {
            if (! count($responseArray)) {
                throw new Exception('CEP não encontrado na base do CEP Aberto.');
            }

            return $responseArray;
        };
    }

    private function extractCepValuesFromResponse()
    {
        return function (array $responseArray) {
            return [
                'zipCode' => $responseArray['cep'],
                'state' => $responseArray['estado'],
                'city' => $responseArray['cidade'],
                'district' => $responseArray['bairro'],
                'street' => $responseArray['logradouro'],
            ];
        };
    }

    private function throwApplicationError()
    {
        return function (Exception $exception) {
            if ($exception instanceof RequestException) {
                $message = 'Erro ao se conectar com o serviço CEP Aberto.';
            }

            throw new CepPromiseProviderException(
                $message ?? $exception->getMessage(),
                $this->providerIdentifier
            );
        };
    }
}
