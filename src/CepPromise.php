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
    const CEP_SIZE = 8;

    const ERROR_PROVIDER_CODE = 2;

    const ERROR_VALIDATION_CODE = 1;

    /**
     * Busca as informações referente ao CEP informado.
     *
     * @param  string|int  $cep
     * @return Address
     *
     * @throws CepPromiseException
     */
    public static function fetch($cep)
    {
        return (new self())->run($cep);
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
            $promises = array_merge(
                ViaCepProvider::createPromiseArray($cepWithLeftPad),
                CepAbertoProvider::createPromiseArray($cepWithLeftPad),
                CorreiosProvider::createPromiseArray($cepWithLeftPad)
            );

            return Promise\any($promises);
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
