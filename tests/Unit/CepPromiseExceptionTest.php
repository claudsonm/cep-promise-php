<?php

namespace Claudsonm\CepPromise\Tests\Unit;

use Claudsonm\CepPromise\Exceptions\CepPromiseException;
use Claudsonm\CepPromise\Exceptions\CepPromiseProviderException;
use PHPUnit\Framework\TestCase;

class CepPromiseExceptionTest extends TestCase
{
    public function testItCanDisplayTheExceptionAsAnArrayWhenPassingSimpleErrorArray()
    {
        $e = new CepPromiseException(
            'Mensagem de erro principal vindo do teste',
            2,
            [
                [
                    'message' => 'Mensagem descritiva com detalhes do erro.',
                    'service' => 'cep_validation',
                ],
            ]
        );

        $expectedData = [
            'message' => 'Mensagem de erro principal vindo do teste',
            'code' => 2,
            'errors' => [
                [
                    'message' => 'Mensagem descritiva com detalhes do erro.',
                    'service' => 'cep_validation',
                ],
            ],
        ];

        $this->assertSame($expectedData, $e->toArray());
    }

    public function testItCanDisplayTheExceptionAsAnArrayWhenPassingProviderExceptionsErrorArray()
    {
        $e = new CepPromiseException(
            'Mensagem de erro principal fake',
            1,
            [
                new CepPromiseProviderException('Mensagem de falha da exceção do provider 1', 'excecao_provider_1'),
                new CepPromiseProviderException('Mensagem de falha da exceção do provider 2', 'excecao_provider_2'),
            ]
        );

        $expectedData = [
            'message' => 'Mensagem de erro principal fake',
            'code' => 1,
            'errors' => [
                [
                    'provider' => 'excecao_provider_1',
                    'message' => 'Mensagem de falha da exceção do provider 1',
                ],
                [
                    'provider' => 'excecao_provider_2',
                    'message' => 'Mensagem de falha da exceção do provider 2',
                ],
            ],
        ];

        $this->assertSame($expectedData, $e->toArray());
    }
}
