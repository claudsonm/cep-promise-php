<?php

namespace Claudsonm\CepPromise\Contracts;

interface ProviderInterface
{
    /**
     * Cria a Promise para obter os dados de um CEP no provedor do serviço.
     *
     * @param string $cep
     *
     * @return \GuzzleHttp\Promise\Promise
     */
    public function makePromise(string $cep);
}
