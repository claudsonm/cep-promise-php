<?php

namespace Claudsonm\CepPromise;

use Claudsonm\CepPromise\Exceptions\CepPromiseException;
use Claudsonm\CepPromise\Providers\CepAbertoProvider;
use Claudsonm\CepPromise\Providers\CorreiosProvider;
use Claudsonm\CepPromise\Providers\ViaCepProvider;
use Exception;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\FulfilledPromise;

/**
 * Efetua a consulta pelas informações de um CEP em diferentes serviços de
 * forma concorrente, retornando a resposta mais rápida..
 *
 * @author Claudson Martins <claudson@outlook.com>
 */
class CepPromise
{
    private const CEP_SIZE = 8;

    private const ERROR_PROVIDER_CODE = 2;

    private const ERROR_VALIDATION_CODE = 1;

    /**
     * Array com todos os FQCN dos providers onde os CEPs serão consultados.
     *
     * @var array|string[]
     */
    protected array $providers;

    public function __construct(array $providers = [])
    {
        $this->providers = ! empty($providers) ? $providers : [
            ViaCepProvider::class,
            CepAbertoProvider::class,
            CorreiosProvider::class,
        ];
    }

    /**
     * Busca as informações referente ao CEP informado.
     *
     * @param  string|int  $cep
     * @param  string[]    $providers
     * @return Address
     *
     * @throws CepPromiseException
     */
    public static function fetch($cep, array $providers = [])
    {
        return (new self($providers))->run($cep);
    }

    /**
     * Dispara a cadeia de execução para obtenção das informações do CEP dado.
     *
     * @param  string|int  $cepRawValue
     * @return Address
     */
    public function run($cepRawValue): Address
    {
        $promise = new FulfilledPromise($cepRawValue);
        $cepData = $promise
            ->then(call_user_func([__CLASS__, 'validateInputType']))
            ->then(call_user_func([__CLASS__, 'removeSpecialCharacters']))
            ->then(call_user_func([__CLASS__, 'validateInputLength']))
            ->then(call_user_func([__CLASS__, 'leftPadWithZeros']))
            ->then(call_user_func([__CLASS__, 'fetchCepFromProviders']))
            ->otherwise(call_user_func([__CLASS__, 'handleProvidersError']))
            ->otherwise(call_user_func([__CLASS__, 'throwApplicationError']))
            ->wait();

        return Address::create($cepData);
    }

    private function validateInputType()
    {
        return function ($cepRawValue) {
            if (is_string($cepRawValue) || is_int($cepRawValue)) {
                return $cepRawValue;
            }

            throw new CepPromiseException(
                'Erro ao inicializar a instância do CepPromise.',
                self::ERROR_VALIDATION_CODE,
                [
                    [
                        'message' => 'Você deve informar o CEP utilizando uma string ou um inteiro.',
                        'service' => 'cep_validation',
                    ],
                ]
            );
        };
    }

    private function removeSpecialCharacters()
    {
        return function (string $cepRawValue) {
            return preg_replace('/\D+/', '', $cepRawValue);
        };
    }

    private function validateInputLength()
    {
        return function (string $cepNumbers) {
            if (strlen($cepNumbers) <= self::CEP_SIZE) {
                return $cepNumbers;
            }

            throw new CepPromiseException(
                'CEP deve conter exatamente '.self::CEP_SIZE.' caracteres.',
                self::ERROR_VALIDATION_CODE,
                [
                    [
                        'message' => 'CEP informado possui mais do que '.self::CEP_SIZE.' caracteres.',
                        'service' => 'cep_validation',
                    ],
                ]
            );
        };
    }

    private function leftPadWithZeros()
    {
        return function (string $cepSanitized) {
            return str_pad($cepSanitized, self::CEP_SIZE, '0', STR_PAD_LEFT);
        };
    }

    private function fetchCepFromProviders()
    {
        return function (string $cepWithLeftPad) {
            $promises = [];
            foreach ($this->providers as $provider) {
                $promises = array_merge($promises, $provider::createPromiseArray($cepWithLeftPad));
            }

            return Promise\Utils::any($promises);
        };
    }

    private function handleProvidersError()
    {
        return function (Exception $onRejected) {
            if ($onRejected instanceof Promise\AggregateException) {
                throw new CepPromiseException(
                    'Todos os serviços de CEP retornaram erro.',
                    self::ERROR_PROVIDER_CODE,
                    $onRejected->getReason()
                );
            }

            throw $onRejected;
        };
    }

    private function throwApplicationError()
    {
        return function (Exception $exception) {
            throw new CepPromiseException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getErrors() ?? []
            );
        };
    }
}
