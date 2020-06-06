# CEP Promise PHP
[![Build Status](https://travis-ci.org/claudsonm/cep-promise-php.svg?branch=master)](https://travis-ci.org/claudsonm/cep-promise-php)
[![StyleCI](https://github.styleci.io/repos/177436507/shield?branch=master)](https://github.styleci.io/repos/177436507)
![Packagist](https://img.shields.io/packagist/dt/claudsonm/cep-promise-php?style=flat-square)
[![All Contributors](https://img.shields.io/badge/all_contributors-1-orange.svg?style=flat-square)](#contributors)

Um pacote agnóstico para PHP 7.0+ que efetua a busca de CEPs em diversos serviços utilizando [Promises/A+](https://promisesaplus.com/). 
Inspirado no pacote [CEP Promise](https://github.com/filipedeschamps/cep-promise) 
para Node.js e web browsers.

## Features
- Realiza requests de forma concorrente, retornando sempre a resposta mais rápida;
- Possui alta disponibilidade por utilizar diversos provedores de serviço diferentes;
- Base de CEPs sempre atualizada, já que conecta-se com diversos serviços, dentre eles os Correios;
- Sem limites de requisições (*rate limits*) conhecidas;
- Feito utilizando a implementação para PHP de promises do pacote [guzzle/promises](https://github.com/guzzle/promises);

## Instalação
A maneira recomendada de instalar o pacote é via [Composer](https://getcomposer.org/download/).
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

### Exemplo 2
Busca utilizando uma string numérica e resposta em forma de array.

```php
<?php

use Claudsonm\CepPromise\CepPromise;

require 'vendor/autoload.php';

$address = CepPromise::fetch('78710857')->toArray();

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

$address = CepPromise::fetch('59067-540');
echo $address->zipCode;
echo $address->street;
echo $address->district;
echo $address->city;
echo $address->state;

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

## Contribuidores

Um muito obrigado a todos os serumaninhos que contribuiram com este repositório:

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore -->
<table><tr><td align="center"><a href="https://github.com/claudsonm"><img src="https://avatars3.githubusercontent.com/u/4139808?v=4" width="100px;" alt="Claudson Martins"/><br /><sub><b>Claudson Martins</b></sub></a><br /><a href="#projectManagement-claudsonm" title="Project Management">📆</a> <a href="#maintenance-claudsonm" title="Maintenance">🚧</a> <a href="https://github.com/claudsonm/cep-promise-php/commits?author=claudsonm" title="Code">💻</a> <a href="https://github.com/claudsonm/cep-promise-php/commits?author=claudsonm" title="Documentation">📖</a> <a href="#example-claudsonm" title="Examples">💡</a> <a href="#business-claudsonm" title="Business development">💼</a> <a href="#tutorial-claudsonm" title="Tutorials">✅</a></td></tr></table>

<!-- ALL-CONTRIBUTORS-LIST:END -->

Este projeto segue a especificação do [all-contributors](https://github.com/all-contributors/all-contributors). Contribuições de qualquer natureza são bem vindas!
