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
 * Classe responsável por receber o CEP e disparar as requisições aos providers.
 *
 * @author Claudson Martins <claudson@outlook.com>
 */
class CepPromise
{
    const CEP_SIZE = 8;

    const ERROR_PROVIDER_CODE = 2;

    const ERROR_VALIDATION_CODE = 1;

    /**
     * Normaliza o CEP dado e efetua as requisições.
     *
     * @param $cepRawValue
     *
     * @throws \Claudsonm\CepPromise\Exceptions\CepPromiseException
     *
     * @return \Claudsonm\CepPromise\Address
     */
    public static function fetch($cepRawValue)
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

    private static function fetchCepFromProviders()
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

    private static function handleProvidersError()
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

    private static function leftPadWithZeros()
    {
        return function (string $cepCleanValue) {
            return str_pad($cepCleanValue, self::CEP_SIZE, '0', STR_PAD_LEFT);
        };
    }

    private static function removeSpecialCharacters()
    {
        return function (string $cepRawValue) {
            return preg_replace('/\D+/', '', $cepRawValue);
        };
    }

    private static function throwApplicationError()
    {
        return function (Exception $exception) {
            throw new CepPromiseException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getErrors() ?? []
            );
        };
    }

    private static function validateInputLength()
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

    private static function validateInputType()
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
}
