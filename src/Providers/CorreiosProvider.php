<?php

namespace Claudsonm\CepPromise\Providers;

use Claudsonm\CepPromise\Contracts\BaseProvider;
use Psr\Http\Message\ResponseInterface;

class CorreiosProvider extends BaseProvider
{
    /**
     * O nome identificador do provedor de serviço.
     *
     * @var string
     */
    public $providerIdentifier = 'correios';

    /**
     * Cria a Promise para obter os dados de um CEP no provedor do serviço.
     *
     * @param string $cep
     *
     * @return \GuzzleHttp\Promise\Promise
     */
    public function makePromise(string $cep)
    {
        $url = 'https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente';
        $httpVerb = 'POST';
        $options = [
            'headers' => [
                'Content-Type' => 'application/xml',
                'cache-control' => 'no-cache',
            ],
            'body' => "<?xml version=\"1.0\"?><soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:cli=\"http://cliente.bean.master.sigep.bsb.correios.com.br/\"><soapenv:Header /><soapenv:Body><cli:consultaCEP><cep>$cep</cep></cli:consultaCEP></soapenv:Body></soapenv:Envelope>",
        ];
        $this->promise = $this->client->requestAsync($httpVerb, $url, $options)
            ->then(call_user_func([__CLASS__, 'analyzeAndParseResponse']))
            ->then(call_user_func([__CLASS__, 'extractValuesFromSuccessResponse']))
            ->then(call_user_func([__CLASS__, 'createAddressObject']));

        return $this->promise;
    }

    private function analyzeAndParseResponse()
    {
        return function (ResponseInterface $response) {
            $xmlString = $response->getBody()->getContents();
            // Remove os dois pontos de tags no formato <xxx:yyy>
            $xmlSanitized = preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3', $xmlString);
            $xmlObject = simplexml_load_string($xmlSanitized);

            return json_decode(json_encode($xmlObject), true);
        };
    }

    private function extractValuesFromSuccessResponse()
    {
        return function (array $soapResponse) {
            return [
                'zipCode' => $soapResponse['soapBody']['ns2consultaCEPResponse']['return']['cep'],
                'state' => $soapResponse['soapBody']['ns2consultaCEPResponse']['return']['uf'],
                'city' => $soapResponse['soapBody']['ns2consultaCEPResponse']['return']['cidade'],
                'district' => $soapResponse['soapBody']['ns2consultaCEPResponse']['return']['bairro'],
                'street' => $soapResponse['soapBody']['ns2consultaCEPResponse']['return']['end'],
            ];
        };
    }
}
