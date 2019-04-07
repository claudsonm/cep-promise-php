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
        $addressRetrieved = CepPromise::fetch(5010000);
        $this->assertInstanceOf(Address::class, $addressRetrieved);

        return $addressRetrieved;
    }

    public function testFetchingUsingValidStringWithLeadingZeros()
    {
        $addressRetrieved = CepPromise::fetch('05010000');
        $this->assertInstanceOf(Address::class, $addressRetrieved);

        return $addressRetrieved;
    }

    /**
     * @depends testFetchingUsingValidIntegerWithoutLeadingZeros
     *
     * @param \Claudsonm\CepPromise\Address $address
     */
    public function testInformationRetrievedFromIntegerWithoutLeadingZeros(Address $address)
    {
        $this->assertEquals('São Paulo', $address->city);
        $this->assertEquals('Perdizes', $address->district);
        $this->assertEquals('SP', $address->state);
        $this->assertEquals('Rua Caiubi', $address->street);
        $this->assertEquals('05010000', $address->zipCode);
    }

    /**
     * @depends testFetchingUsingValidStringWithLeadingZeros
     *
     * @param \Claudsonm\CepPromise\Address $address
     */
    public function testInformationRetrievedFromStringWithLeadingZeros(Address $address)
    {
        $this->assertEquals('São Paulo', $address->city);
        $this->assertEquals('Perdizes', $address->district);
        $this->assertEquals('SP', $address->state);
        $this->assertEquals('Rua Caiubi', $address->street);
        $this->assertEquals('05010000', $address->zipCode);
    }
}
