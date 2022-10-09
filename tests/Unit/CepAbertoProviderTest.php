<?php

namespace Claudsonm\CepPromise\Tests\Unit;

use Claudsonm\CepPromise\Exceptions\CepPromiseProviderException;
use Claudsonm\CepPromise\Providers\CepAbertoProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class CepAbertoProviderTest extends TestCase
{
    public function testItReturnsCepAbertoApiErrorWhenTheProvidedTokenIsExpired()
    {
        $this->expectException(CepPromiseProviderException::class);
        $this->expectExceptionMessage('Erro ao se conectar com o serviço CEP Aberto.');

        $body = 'HTTP Token: Access denied.';
        $mock = new MockHandler([
            new Response(401, [], $body),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new CepAbertoProvider($client);
        $promise = $viaCep->makePromise(49048370);
        $promise->wait();
    }

    public function testItProcessCepAbertoResponseForInvalidCep()
    {
        $this->expectException(CepPromiseProviderException::class);
        $this->expectExceptionMessage('CEP não encontrado na base do CEP Aberto.');

        $body = "{}";
        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new CepAbertoProvider($client);
        $promise = $viaCep->makePromise(8542130);
        $promise->wait();
    }

    public function testItProcessCepAbertoResponseForValidButNonExistentCep()
    {
        $this->expectException(CepPromiseProviderException::class);
        $this->expectExceptionMessage('CEP não encontrado na base do CEP Aberto.');

        $body = "{}";
        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new CepAbertoProvider($client);
        $promise = $viaCep->makePromise(99999999);
        $promise->wait();
    }

    public function testItProcessCepAbertoResponseForValidCep()
    {
        $body = <<<BODY
{
    "altitude": 7.9,
    "cep": "49048370",
    "latitude": "-10.9441067",
    "longitude": "-37.0755725",
    "logradouro": "Rua Vereador Etelvino Barreto",
    "bairro": "Luzia",
    "cidade": {
        "ddd": 79,
        "ibge": "2800308",
        "nome": "Aracaju"
    },
    "estado": {
        "sigla": "SE"
    }
}
BODY;

        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new CepAbertoProvider($client);
        $promise = $viaCep->makePromise(49048370);
        $addressData = $promise->wait();

        $expectedAddress = [
            'zipCode' => '49048370',
            'state' => ['sigla' => 'SE'],
            'city' => [
                "ddd" => 79,
                "ibge" => "2800308",
                "nome" => "Aracaju",
            ],
            'district' => 'Luzia',
            'street' => 'Rua Vereador Etelvino Barreto',
            'provider' => 'cep_aberto',
        ];

        $this->assertSame($expectedAddress, $addressData);
    }
}
