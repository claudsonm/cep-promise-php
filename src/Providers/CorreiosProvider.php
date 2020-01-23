<?php

namespace Claudsonm\CepPromise\Providers;

use Claudsonm\CepPromise\Contracts\BaseProvider;
use Claudsonm\CepPromise\Exceptions\CepPromiseProviderException;
use Exception;
use GuzzleHttp\Exception\RequestException;
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
                'Cache-Control' => 'no-cache',
            ],
            'body' => "<?xml version=\"1.0\"?><soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:cli=\"http://cliente.bean.master.sigep.bsb.correios.com.br/\"><soapenv:Header /><soapenv:Body><cli:consultaCEP><cep>$cep</cep></cli:consultaCEP></soapenv:Body></soapenv:Envelope>",
        ];
        $this->promise = $this->client->requestAsync($httpVerb, $url, $options)
            ->then(call_user_func([__CLASS__, 'analyzeAndParseResponse']))
            ->then(call_user_func([__CLASS__, 'extractValuesFromSuccessResponse']))
            ->otherwise(call_user_func([__CLASS__, 'parseAndExtractErrorMessage']))
            ->otherwise(call_user_func([__CLASS__, 'throwApplicationError']));

        return $this->promise;
    }

    private function analyzeAndParseResponse()
    {
        return function (ResponseInterface $response) {
            $xmlString = $response->getBody()->getContents();

            return $this->soapXmlToArray($xmlString);
        };
    }

    private function extractValuesFromSuccessResponse()
    {
        return function (array $soapResponse) {
            $responseArray = $soapResponse['soapBody']['ns2consultaCEPResponse']['return'] ?? [];
            if (! empty($responseArray)) {
                return [
                    'zipCode' => $responseArray['cep'],
                    'state' => $responseArray['uf'],
                    'city' => $responseArray['cidade'],
                    'district' => $responseArray['bairro'],
                    'street' => $responseArray['end'],
                ];
            }

            throw new Exception('A busca pelo CEP informado não retornou resultados.');
        };
    }

    private function parseAndExtractErrorMessage()
    {
        return function (Exception $exception) {
            if ($exception instanceof RequestException) {
                $xmlString = $exception->getResponse()->getBody()->getContents();
                $fault = $this->soapXmlToArray($xmlString);
                $defaultMessage = 'Erro ao se conectar com o serviço dos Correios.';

                throw new Exception($fault['soapBody']['soapFault']['faultstring'] ?? $defaultMessage);
            }

            throw $exception;
        };
    }

    /**
     * Converte a string de um XML SOAP em um array, removendo os dois pontos
     * de tags no formato <xxx:yyy>.
     *
     * @param string $xml
     *
     * @throws \Exception
     *
     * @return array
     */
    private function soapXmlToArray(string $xml)
    {
        $encodedXml = utf8_encode($xml);
        $xmlSanitized = preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3', $encodedXml);
        $xmlObject = @simplexml_load_string($xmlSanitized);
        if (false === $xmlObject) {
            throw new Exception('Não foi possível interpretar o XML de resposta.');
        }

        return json_decode(json_encode($xmlObject), true);
    }

    private function throwApplicationError()
    {
        return function (Exception $exception) {
            throw new CepPromiseProviderException(
                $exception->getMessage(),
                $this->providerIdentifier
            );
        };
    }
}
