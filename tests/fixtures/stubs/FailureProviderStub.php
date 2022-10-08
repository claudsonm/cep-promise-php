<?php

namespace Claudsonm\CepPromise\Tests\fixtures\stubs;

use GuzzleHttp\Promise\Promise;
use Claudsonm\CepPromise\Contracts\BaseProvider;
use Claudsonm\CepPromise\Exceptions\CepPromiseException;

class FailureProviderStub extends BaseProvider
{
    public $providerIdentifier = 'faliure_provider';

    public function makePromise(string $cep)
    {
        $exception = new CepPromiseException('Dummy failure reason');

        $p = new Promise();
        $p->reject($exception);
        $this->promise = $p;
    }
}
