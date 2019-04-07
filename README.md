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
