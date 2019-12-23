---
title: Instruções de Instalação
description: Passo a passo de como instalar o pacote em seu projeto
extends: _layouts.documentation
section: content
---

Para saber mais sobre o CEP Promise PHP, visite a página contendo algumas informações 
[sobre o pacote](/docs/about).

# Instruções de Instalação {#installing}

A maneira mais recomendada de instalar o pacote é via Composer. Obtê-lo é tão simples 
quanto executar o seguinte comando: `composer require claudsonm/cep-promise-php`.

Após instalar você precisará importar o autoloader do Composer em seus scripts PHP
para ter acesso às dependências instaladas através dele. Para isso adicione `require 'vendor/autoload.php';` 
nos arquivos PHP nos quais deseja utilizar qualquer uma das dependências.
