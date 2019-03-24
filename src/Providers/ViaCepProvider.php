<?php

namespace Claudsonm\CepPromise\Providers;

use Claudsonm\CepPromise\Contracts\BaseProvider;
use Claudsonm\CepPromise\Exceptions\CepPromiseException;
use Psr\Http\Message\ResponseInterface;

class ViaCepProvider extends BaseProvider
{
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
            ->then(call_user_func([__CLASS__, 'createAddressObject']));

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
        return function (array $responseData) {
            if (isset($responseData['erro']) && true === $responseData['erro']) {
                throw new CepPromiseException('CEP não encontrado na base do ViaCEP.');
            }

            return $responseData;
        };
    }

    private function extractCepValuesFromResponse()
    {
        return function (array $responseObject) {
            return [
                'zipCode' => str_replace('-', '', $responseObject['cep']),
                'state' => $responseObject['uf'],
                'city' => $responseObject['localidade'],
                'district' => $responseObject['bairro'],
                'street' => $responseObject['logradouro'],
            ];
        };
    }
}
