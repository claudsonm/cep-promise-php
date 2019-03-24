<?php

namespace Claudsonm\CepPromise;

/**
 * Classe que encapsula toda a informação de um endereço.
 *
 * @author Claudson Martins <claudson@outlook.com>
 */
class Address
{
    /**
     * A cidade.
     *
     * @var string
     */
    public $city;

    /**
     * O bairro.
     *
     * @var string
     */
    public $district;

    /**
     * A unidade federativa (UF).
     *
     * @var string
     */
    public $state;

    /**
     * O logradouro.
     *
     * @var string
     */
    public $street;
    /**
     * O código do CEP.
     *
     * @var string
     */
    public $zipCode;

    /**
     * Cria uma nova instância da classe de endereços.
     *
     * @param array $data
     *
     * @return \Claudsonm\CepPromise\Address
     */
    public static function create(array $data = [])
    {
        $address = new self();
        foreach (get_object_vars($address) as $name => $currentValue) {
            $address->{$name} = $data[$name] ?? null;
        }

        return $address->toArray();
    }

    public function toArray()
    {
        return json_encode($this);
    }
}
