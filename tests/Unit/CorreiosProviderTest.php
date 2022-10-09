<?php

namespace Claudsonm\CepPromise\Tests\Unit;

use Claudsonm\CepPromise\Exceptions\CepPromiseProviderException;
use Claudsonm\CepPromise\Providers\CorreiosProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class CorreiosProviderTest extends TestCase
{
    public function testItHandlesCorreiosResponseWithNonXml()
    {
        $this->expectException(CepPromiseProviderException::class);
        $this->expectExceptionMessage('Não foi possível interpretar o XML de resposta.');

        $headers = [
            'Content-Type' => 'text/plain;charset=ISO-8859-1',
        ];
        $body = 'Error';
        $mock = new MockHandler([
            new Response(500, $headers, $body),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new CorreiosProvider($client);
        $promise = $viaCep->makePromise(111111);
        $promise->wait();
    }

    public function testItProcessCorreiosResponseForInvalidCep()
    {
        $this->expectException(CepPromiseProviderException::class);
        $this->expectExceptionMessage('CEP INVÁLIDO');

        $headers = [
            'Content-Type' => 'text/xml;charset=ISO-8859-1',
        ];
        $body = <<<BODY
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <soap:Fault>
            <faultcode>soap:Server</faultcode>
            <faultstring>CEP INVÁLIDO</faultstring>
            <detail>
                <ns2:SigepClienteException xmlns:ns2="http://cliente.bean.master.sigep.bsb.correios.com.br/">CEP INVÁLIDO</ns2:SigepClienteException>
            </detail>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>
BODY;
        $mock = new MockHandler([
            new Response(500, $headers, $body),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new CorreiosProvider($client);
        $promise = $viaCep->makePromise(8542130);
        $promise->wait();
    }

    public function testItProcessCorreiosResponseForValidButNonExistentCep()
    {
        $this->expectException(CepPromiseProviderException::class);
        $this->expectExceptionMessage('CEP INVÁLIDO');

        $headers = [
            'Content-Type' => 'text/xml;charset=ISO-8859-1',
        ];
        $body = <<<BODY
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <soap:Fault>
            <faultcode>soap:Server</faultcode>
            <faultstring>CEP INVÁLIDO</faultstring>
            <detail>
                <ns2:SigepClienteException xmlns:ns2="http://cliente.bean.master.sigep.bsb.correios.com.br/">CEP INVÁLIDO</ns2:SigepClienteException>
            </detail>
        </soap:Fault>
    </soap:Body>
</soap:Envelope>
BODY;
        $mock = new MockHandler([
            new Response(500, $headers, $body),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new CorreiosProvider($client);
        $promise = $viaCep->makePromise(99999999);
        $promise->wait();
    }

    public function testItProcessCorreiosResponseForValidExistingButGenericCep()
    {
        $this->expectException(CepPromiseProviderException::class);
        $this->expectExceptionMessage('A busca pelo CEP informado não retornou resultados.');

        $headers = [
            'Content-Type' => 'text/xml;charset=ISO-8859-1',
        ];
        $body = <<<BODY
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <ns2:consultaCEPResponse xmlns:ns2="http://cliente.bean.master.sigep.bsb.correios.com.br/"/>
    </soap:Body>
</soap:Envelope>
BODY;
        $mock = new MockHandler([
            new Response(200, $headers, $body),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new CorreiosProvider($client);
        $promise = $viaCep->makePromise(49000000);
        $promise->wait();
    }

    public function testItProcessCorreiosResponseForValidCep()
    {
        $body = <<<BODY
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <ns2:consultaCEPResponse xmlns:ns2="http://cliente.bean.master.sigep.bsb.correios.com.br/">
            <return>
                <bairro>Luzia</bairro>
                <cep>49048370</cep>
                <cidade>Aracaju</cidade>
                <complemento2></complemento2>
                <end>Rua Vereador Etelvino Barreto</end>
                <uf>SE</uf>
            </return>
        </ns2:consultaCEPResponse>
    </soap:Body>
</soap:Envelope>
BODY;

        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new CorreiosProvider($client);
        $promise = $viaCep->makePromise(49048370);
        $addressData = $promise->wait();

        $expectedAddress = [
            'zipCode' => '49048370',
            'state' => 'SE',
            'city' => 'Aracaju',
            'district' => 'Luzia',
            'street' => 'Rua Vereador Etelvino Barreto',
            'provider' => 'correios',
        ];

        $this->assertSame($expectedAddress, $addressData);
    }
}
