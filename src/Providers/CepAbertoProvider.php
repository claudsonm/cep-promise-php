<?php

namespace Claudsonm\CepPromise\Providers;

use Claudsonm\CepPromise\Contracts\BaseProvider;
use Claudsonm\CepPromise\Exceptions\CepPromiseException;
use Claudsonm\CepPromise\Exceptions\CepPromiseProviderException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class CepAbertoProvider extends BaseProvider
{
    const CEP_ABERTO_TOKEN = '37d718d2984e6452584a76d3d59d3a26';

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
            ->then(call_user_func([__CLASS__, 'createAddressObject']))
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
        return function (array $responseObject) {
            if (! count($responseObject)) {
                throw new CepPromiseException('CEP não encontrado na base do Cep Aberto.');
            }

            return $responseObject;
        };
    }

    private function extractCepValuesFromResponse()
    {
        return function (array $responseObject) {
            return [
                'zipCode' => $responseObject['cep'],
                'state' => $responseObject['estado'],
                'city' => $responseObject['cidade'],
                'district' => $responseObject['bairro'],
                'street' => $responseObject['logradouro'],
            ];
        };
    }

    private function throwApplicationError()
    {
        return function (RequestException $error) {
            throw new CepPromiseProviderException($error->getMessage(), $this->providerIdentifier);
        };
    }
}
