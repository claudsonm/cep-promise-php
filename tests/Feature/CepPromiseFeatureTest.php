<?php

namespace Claudsonm\CepPromise\Tests\Feature;

use Claudsonm\CepPromise\Address;
use Claudsonm\CepPromise\CepPromise;
use Claudsonm\CepPromise\Exceptions\CepPromiseException;
use PHPUnit\Framework\TestCase;

class CepPromiseFeatureTest extends TestCase
{
    public function testExceptionFetchingInvalidCep()
    {
        $this->expectException(CepPromiseException::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('CEP deve conter exatamente 8 caracteres.');
        CepPromise::fetch('123456789');
    }

    public function testExceptionFetchingNonExistentCep()
    {
        $this->expectException(CepPromiseException::class);
        $this->expectExceptionCode(2);
        $this->expectExceptionMessage('Todos os serviços de CEP retornaram erro.');
        CepPromise::fetch('99999999');
    }

    public function testFetchingUsingValidIntegerWithoutLeadingZeros()
    {
        $address = CepPromise::fetch(8542130);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('Ferraz de Vasconcelos', $address->city);
        $this->assertEquals('Cidade Kemel', $address->district);
        $this->assertEquals('SP', $address->state);
        $this->assertEquals('Avenida Luiz Rosa da Costa', $address->street);
        $this->assertEquals('08542130', $address->zipCode);
    }

    public function testFetchingUsingValidStringWithLeadingZeros()
    {
        $address = CepPromise::fetch('05010000');
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('São Paulo', $address->city);
        $this->assertEquals('Perdizes', $address->district);
        $this->assertEquals('SP', $address->state);
        $this->assertEquals('Rua Caiubi', $address->street);
        $this->assertEquals('05010000', $address->zipCode);
    }
}
