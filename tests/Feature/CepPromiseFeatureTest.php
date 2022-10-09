<?php

namespace Claudsonm\CepPromise\Tests\Feature;

use Claudsonm\CepPromise\Address;
use Claudsonm\CepPromise\CepPromise;
use Claudsonm\CepPromise\Exceptions\CepPromiseException;
use Claudsonm\CepPromise\Tests\fixtures\stubs\FailureProviderStub;
use Claudsonm\CepPromise\Tests\fixtures\stubs\SuccessfulProviderStub;
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
        CepPromise::fetch('99999999', [FailureProviderStub::class]);
    }

    public function testFetchingUsingValidIntegerWithoutLeadingZeros()
    {
        $address = CepPromise::fetch(8542130, [SuccessfulProviderStub::class]);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('Ferraz de Vasconcelos', $address->city);
        $this->assertEquals('Cidade Kemel', $address->district);
        $this->assertEquals('SP', $address->state);
        $this->assertEquals('Avenida Luiz Rosa da Costa', $address->street);
        $this->assertEquals('08542130', $address->zipCode);
    }

    public function testFetchingUsingValidStringWithLeadingZeros()
    {
        $address = CepPromise::fetch('05010000', [SuccessfulProviderStub::class]);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('Ferraz de Vasconcelos', $address->city);
        $this->assertEquals('Cidade Kemel', $address->district);
        $this->assertEquals('SP', $address->state);
        $this->assertEquals('Avenida Luiz Rosa da Costa', $address->street);
        $this->assertEquals('08542130', $address->zipCode);
    }

    public function testItCanCastTheAddressToJson()
    {
        $address = CepPromise::fetch('05010000', [SuccessfulProviderStub::class]);
        $this->assertInstanceOf(Address::class, $address);
        $addressJson = '{"city":"Ferraz de Vasconcelos","district":"Cidade Kemel","state":"SP","street":"Avenida Luiz Rosa da Costa","zipCode":"08542130","provider":"successful_provider"}';
        $this->assertEquals($addressJson, (string) $address);
    }
}
