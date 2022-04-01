<?php

namespace Claudsonm\CepPromise\Contracts;

use GuzzleHttp\Promise\Promise;

interface ProviderInterface
{
    /**
     * Cria a Promise para obter os dados de um CEP no provedor do serviço.
     *
     * @param  string  $cep
     * @return Promise
     */
    public function makePromise(string $cep);
}
