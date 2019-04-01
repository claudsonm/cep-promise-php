<?php

namespace Claudsonm\CepPromise\Exceptions;

class CepPromiseException extends \Exception
{
    /**
     * @var array
     */
    protected $errors;

    public function __construct(string $message = '', int $code = 0, array $errors = [], \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $errors[] = [
                'provider' => $error->getProvider(),
                'message' => $error->getMessage(),
            ];
        }

        return [
            'message' => $this->message,
            'code' => $this->code,
            'errors' => $errors,
        ];
    }
}
