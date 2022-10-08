<?php

namespace Claudsonm\CepPromise\Tests\fixtures\stubs;

use GuzzleHttp\Promise\Promise;
use Claudsonm\CepPromise\Contracts\BaseProvider;

class SuccessfulProviderStub extends BaseProvider
{
    public $providerIdentifier = 'successful_provider';

    public function makePromise(string $cep)
    {
        $p = new Promise();
        $p->resolve([
            'zipCode' => '08542130',
            'state' => 'SP',
            'city' => 'Ferraz de Vasconcelos',
            'district' => 'Cidade Kemel',
            'street' => 'Avenida Luiz Rosa da Costa',
            'provider' => $this->providerIdentifier,
        ]);
        $this->promise = $p;
    }
}
