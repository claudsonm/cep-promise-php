# CEP Promise PHP

[![Build Status](https://travis-ci.org/claudsonm/cep-promise-php.svg?branch=master)](https://travis-ci.org/claudsonm/cep-promise-php)
[![StyleCI](https://github.styleci.io/repos/177436507/shield?branch=master)](https://github.styleci.io/repos/177436507)

Um pacote para busca de CEPs em diversos serviços utilizando [Promises/A+](https://promisesaplus.com/). 
Inspirado no pacote [CEP Promise](https://github.com/filipedeschamps/cep-promise) 
para Node.js e web browsers.

## Features
- Realiza requests de forma concorrente, retornando sempre a resposta mais rápida;
- Possui alta disponibilidade por utilizar diversos provedores de serviço diferentes;
- Base de CEPs sempre atualizada, já que conecta-se com diversos serviços, dentre eles os Correios;
- Sem limites de requisições (rate limits) conhecidas;
- Feito utilizando a implementação para PHP de promises do pacote [guzzle/promises](https://github.com/guzzle/promises);

## Instalação
A maneira mais recomendada de instalar o pacote é via [Composer](https://getcomposer.org/download/).
Com a ferramenta instalada, execute o comando abaixo:

```bash
composer require claudsonm/cep-promise-php
```

Após instalar, você precisará requerer o autoloader do Composer por meio da
instrução:

```php
require 'vendor/autoload.php';
```

## Exemplos de Uso

### Exemplo 1
Busca utilizando valores inteiros e resposta em forma de objeto.

```php
<?php

use Claudsonm\CepPromise\CepPromise;

require 'vendor/autoload.php';

$addressObject = CepPromise::fetch(49040610);

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

### Exemplo 2
Busca utilizando uma string numérica e resposta em forma de array.

```php
<?php

use Claudsonm\CepPromise\CepPromise;

require 'vendor/autoload.php';

$addressArray = CepPromise::fetch('78710857')->toArray();

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

### Exemplo 3
Busca utilizando uma string com formatação.

```php
<?php

use Claudsonm\CepPromise\CepPromise;

require 'vendor/autoload.php';

$addressObject = CepPromise::fetch('59067-540');
echo $addressObject->zipCode;
echo $addressObject->street;
echo $addressObject->district;
echo $addressObject->city;
echo $addressObject->state;

/*
'59067540'
'Rua Figueira'
'Pitimbu'
'Natal'
'RN'
*/
```

### Exemplo 4
Captura e tratamento de erros.

```php
<?php

use Claudsonm\CepPromise\CepPromise;
use Claudsonm\CepPromise\Exceptions\CepPromiseException;

require 'vendor/autoload.php';

try {
    $response = CepPromise::fetch('99999999');
} catch (CepPromiseException $e) {
    $response = $e->toArray();
}

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
```