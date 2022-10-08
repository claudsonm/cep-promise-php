<?php

namespace Claudsonm\CepPromise\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use Claudsonm\CepPromise\Providers\ViaCepProvider;
use Claudsonm\CepPromise\Exceptions\CepPromiseProviderException;

class ViaCepProviderTest extends TestCase
{
    public function testItReturnsViaCepApiErrorWhenCepWithoutLeadingZeroIsProvided()
    {
        $this->expectException(CepPromiseProviderException::class);
        $this->expectExceptionMessage('Erro ao se conectar com o serviço ViaCEP.');

        $mock = new MockHandler([
            new Response(400),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new ViaCepProvider($client);
        $promise = $viaCep->makePromise(8542130);
        $promise->wait();
    }

    public function testItProcessViaCepApiResponseForNonExistentCep()
    {
        $this->expectException(CepPromiseProviderException::class);
        $this->expectExceptionMessage('CEP não encontrado na base do ViaCEP.');
        $body = <<<BODY
{
  "erro": "true"
}
BODY;

        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new ViaCepProvider($client);
        $promise = $viaCep->makePromise(99999999);
        $promise->wait();
    }

    public function testItProcessViaCepApiResponseForValidCep()
    {
        $body = <<<BODY
{
  "cep": "49048-370",
  "logradouro": "Rua Vereador Etelvino Barreto",
  "complemento": "",
  "bairro": "Luzia",
  "localidade": "Aracaju",
  "uf": "SE",
  "ibge": "2800308",
  "gia": "",
  "ddd": "79",
  "siafi": "3105"
}
BODY;

        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $viaCep = new ViaCepProvider($client);
        $promise = $viaCep->makePromise(99999999);
        $addressData = $promise->wait();

        $expectedAddress = [
            'zipCode' => '49048370',
            'state' => 'SE',
            'city' => 'Aracaju',
            'district' => 'Luzia',
            'street' => 'Rua Vereador Etelvino Barreto',
            'provider' => 'via_cep',
        ];

        $this->assertSame($expectedAddress, $addressData);
    }
}
