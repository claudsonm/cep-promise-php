<?php

namespace Claudsonm\CepPromise\Contracts;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;

abstract class BaseProvider implements ProviderInterface
{
    /**
     * O nome identificador do provedor de serviço.
     *
     * @var string
     */
    public $providerIdentifier = 'base_provider';

    /**
     * O cliente HTTP utilizado para realizar os requests.
     *
     * @var Client
     */
    protected $client;

    /**
     * A instância da Promise para consulta no serviço de busca.
     *
     * @var Promise
     */
    protected $promise;

    public function __construct(Client $client = null)
    {
        $this->client = $client ?? new Client();
    }

    /**
     * Retorna um array associativo com o identificador do provider e a Promise.
     *
     * @param $cep
     * @return array
     */
    public static function createPromiseArray($cep)
    {
        $class = get_called_class();
        $provider = new $class();
        $provider->makePromise($cep);

        return $provider->toArray();
    }

    /**
     * Retorna o provider em um array associativo onde a chave é o
     * identificador e o valor é a Promise.
     *
     * @return array
     */
    public function toArray()
    {
        return [$this->providerIdentifier => $this->promise];
    }
}
