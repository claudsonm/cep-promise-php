<?php

namespace Claudsonm\CepPromise\Exceptions;

class CepPromiseProviderException extends \Exception
{
    /**
     * @var string
     */
    protected $provider;

    public function __construct(string $message = '', string $provider = '')
    {
        parent::__construct($message);
        $this->provider = $provider;
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }
}
