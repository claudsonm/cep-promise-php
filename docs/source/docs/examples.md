---
title: Exemplos de Utilização
description: Exemplos de código de utilização do pacote
extends: _layouts.documentation
section: content
---

# Exemplos de Utilização {#examples}

## Busca por CEP passando inteiros {#search-using-integers}

No exemplo a seguir é feita uma busca utilizando um valor inteiro. Por padrão as 
respostas serão um objeto da classe Address.

```php
<?php

use Claudsonm\CepPromise\CepPromise;

$address = CepPromise::fetch(49040610);

/* 
Claudsonm\CepPromise\Address Object
(
    [city] => Aracaju
    [district] => Inácio Barbosa
    [state] => SE
    [street] => Rua Universo
    [zipCode] => 49040610
)
*/
```

## Busca por CEP passando strings {#search-using-strings}

Geralmente armazenamos CEPs como strings e não como inteiros. Pensando nisso, 
você também pode efetuar as suas buscas passando strings.

Está utilizando máscaras? Sem problemas. Você pode passar a string da forma que 
preferir. Nós faremos os devidos tratamentos antes de performar as requisições.

```php
<?php

use Claudsonm\CepPromise\CepPromise;

$address = CepPromise::fetch('49040610');

/* 
Claudsonm\CepPromise\Address Object
(
    [city] => Aracaju
    [district] => Inácio Barbosa
    [state] => SE
    [street] => Rua Universo
    [zipCode] => 49040610
)
*/

$anotherAddress = CepPromise::fetch('59067-540');

/* 
Claudsonm\CepPromise\Address Object
(
    [city] => Natal
    [district] => Pitimbu
    [state] => RN
    [street] => Rua Figueira
    [zipCode] => 59067540
)
*/

```

## Respostas em formato de array {#respose-as-array}

Acha mais conveniente trabalhar com arrays ao invés de objetos? De boa. Basta 
chamar o método `toArray` em uma instância de Address que faremos a conversão
de tipos. Inclusive, dá pra fazer tudo em uma linha, saca só:

```php
<?php

use Claudsonm\CepPromise\CepPromise;

$address = CepPromise::fetch('78710857')->toArray();
// ou
$address = CepPromise::fetch('78710857');
$address->toArray();

/*
 Array
(
    [city] => Rondonópolis
    [district] => Vila Marinópolis
    [state] => MT
    [street] => Rua Pirajuí
    [zipCode] => 78710857
)
*/
```

## Erros e Exceções {#errors-and-exceptions}

Nem sempre as coisas saem como esperado. Por realizar as requisições de forma 
concorrente e em diversos serviços distintos, a chance de todas as solicitações 
falharem são baixas, porém é algo que pode acontecer. E quando isso ocorrer, 
uma exceção do tipo `CepPromiseException` será lançada, onde você poderá 
capturar e tratar em sua aplicação.

Além de todos os métodos já conhecidos e existentes em qualquer exceção,
adicionamos alguns adicionais que podem ser do seu interesse.

````php
<?php

use Claudsonm\CepPromise\CepPromise;
use Claudsonm\CepPromise\Exceptions\CepPromiseException;

try {
    $response = CepPromise::fetch('99999999');
} catch (CepPromiseException $e) {
    $e->getMessage();
    // Todos os serviços de CEP retornaram erro.
    
    $e->getErrors();
    /*
        Array
        (
            [0] => Array
                (
                    [provider] => via_cep
                    [message] => CEP não encontrado na base do ViaCEP.
                )
    
            [1] => Array
                (
                    [provider] => cep_aberto
                    [message] => Erro ao se conectar com o serviço CEP Aberto.
                )
    
            [2] => Array
                (
                    [provider] => correios
                    [message] => CEP INVÁLIDO
                )
        ) 
     */

    $e->toArray();
    /*
        Array
        (
            [message] => Todos os serviços de CEP retornaram erro.
            [code] => 2
            [errors] => Array
                (
                    [0] => Array
                        (
                            [provider] => via_cep
                            [message] => CEP não encontrado na base do ViaCEP.
                        )
        
                    [1] => Array
                        (
                            [provider] => cep_aberto
                            [message] => Erro ao se conectar com o serviço CEP Aberto.
                        )
        
                    [2] => Array
                        (
                            [provider] => correios
                            [message] => CEP INVÁLIDO
                        )
                )
        )
    */
}
````
