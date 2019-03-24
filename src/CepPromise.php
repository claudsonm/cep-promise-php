<?php

namespace Claudsonm\CepPromise;

use Claudsonm\CepPromise\Exceptions\CepPromiseException;
use Claudsonm\CepPromise\Providers\CepAbertoProvider;
use Claudsonm\CepPromise\Providers\CorreiosProvider;
use Claudsonm\CepPromise\Providers\ViaCepProvider;
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

    const ERROR_VALIDATION = 1;

    public static function find(string $cepRawValue)
    {
        $promise = new FulfilledPromise($cepRawValue);

        return $promise
            ->then(call_user_func([__CLASS__, 'removeSpecialCharacters']))
            ->then(call_user_func([__CLASS__, 'validateInputLength']))
            ->then(call_user_func([__CLASS__, 'leftPadWithZeros']))
            ->then(call_user_func([__CLASS__, 'fetchCepFromProviders']))
            ->wait();
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

    private function leftPadWithZeros()
    {
        return function ($cepCleanValue) {
            return str_pad($cepCleanValue, self::CEP_SIZE, '0', STR_PAD_LEFT);
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
                self::ERROR_VALIDATION,
                [
                    'message' => 'CEP informado possui mais do que '.self::CEP_SIZE.' caracteres.',
                    'service' => 'cep_validation',
                ]
            );
        };
    }
}
