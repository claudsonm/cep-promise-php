<?php

namespace Claudsonm\CepPromise\Providers;

use Claudsonm\CepPromise\Contracts\BaseProvider;
use Claudsonm\CepPromise\Exceptions\CepPromiseProviderException;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class ViaCepProvider extends BaseProvider
{
    /**
     * O nome identificador do provedor de serviço.
     *
     * @var string
     */
    public $providerIdentifier = 'via_cep';

    /**
     * Cria a Promise para obter os dados de um CEP no provedor do serviço.
     *
     * @param string $cep
     *
     * @return \GuzzleHttp\Promise\Promise
     */
    public function makePromise(string $cep)
    {
        $url = "https://viacep.com.br/ws/$cep/json";
        $httpVerb = 'GET';
        $options = [
            'headers' => [
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
            if (isset($responseArray['erro']) && true === $responseArray['erro']) {
                throw new Exception('CEP não encontrado na base do ViaCEP.');
            }

            return $responseArray;
        };
    }

    private function extractCepValuesFromResponse()
    {
        return function (array $responseArray) {
            return [
                'zipCode' => str_replace('-', '', $responseArray['cep']),
                'state' => $responseArray['uf'],
                'city' => $responseArray['localidade'],
                'district' => $responseArray['bairro'],
                'street' => $responseArray['logradouro'],
            ];
        };
    }

    private function throwApplicationError()
    {
        return function (Exception $exception) {
            if ($exception instanceof RequestException) {
                $message = 'Erro ao se conectar com o serviço ViaCEP.';
            }

            throw new CepPromiseProviderException(
                $message ?? $exception->getMessage(),
                $this->providerIdentifier
            );
        };
    }
}
