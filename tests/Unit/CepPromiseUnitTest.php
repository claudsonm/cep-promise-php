<?php

namespace Claudsonm\CepPromise\Tests\Unit;

use Claudsonm\CepPromise\Address;
use Claudsonm\CepPromise\CepPromise;
use Claudsonm\CepPromise\Exceptions\CepPromiseException;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class CepPromiseUnitTest extends TestCase
{
    public function testErrorTryingToFetchPassingAFunction()
    {
        $this->expectException(CepPromiseException::class);
        $this->expectExceptionMessage('Erro ao inicializar a instância do CepPromise.');
        $this->expectExceptionCode(1);
        CepPromise::fetch(function () {
            return 'You shall not pass!';
        });
    }

    public function testErrorTryingToFetchPassingAnArray()
    {
        $this->expectException(CepPromiseException::class);
        $this->expectExceptionMessage('Erro ao inicializar a instância do CepPromise.');
        $this->expectExceptionCode(1);
        CepPromise::fetch([53020665]);
    }

    public function testErrorTryingToFetchPassingAnObject()
    {
        $this->expectException(CepPromiseException::class);
        $this->expectExceptionMessage('Erro ao inicializar a instância do CepPromise.');
        $this->expectExceptionCode(1);
        CepPromise::fetch((object) ['top_gear' => '1000', 'gta' => '900']);
    }

    public function testErrorTryingToFetchWithoutArgument()
    {
        $this->expectException(\ArgumentCountError::class);
        CepPromise::fetch();
    }

    public function testIfFetchMethodExists()
    {
        $this->assertTrue(method_exists(CepPromise::class, 'fetch'));
    }

    public function testIfFetchMethodIsStatic()
    {
        $fetchMethod = new ReflectionMethod(CepPromise::class, 'fetch');
        $this->assertTrue($fetchMethod->isStatic());
    }

    public function testAnAddressCanBeConvertedToAnArray()
    {
        $address = $this->getTestAddressAsObject();
        $this->assertEqualsCanonicalizing($this->getTestAddressAsArray(), $address->toArray());
    }

    public function testAnAddressCanBeCreatedFromAnArray()
    {
        $address = Address::create($this->getTestAddressAsArray());
        $this->assertEqualsCanonicalizing($this->getTestAddressAsObject(), $address);
    }

    /**
     * Retorna o endereço de teste na forma de um array associativo.
     *
     * @return array
     */
    private function getTestAddressAsArray(): array
    {
        return [
            'city' => 'Aracaju',
            'district' => 'Santo Antônio',
            'state' => 'SE',
            'street' => 'Avenida Presidente Juscelino Kubitschek',
            'zipCode' => '49060535',
        ];
    }

    /**
     * Retorna o endereço de teste na forma de um objeto.
     *
     * @return Address
     */
    private function getTestAddressAsObject(): Address
    {
        $address = new Address();
        $address->city = 'Aracaju';
        $address->state = 'SE';
        $address->street = 'Avenida Presidente Juscelino Kubitschek';
        $address->district = 'Santo Antônio';
        $address->zipCode = '49060535';

        return $address;
    }
}
