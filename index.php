<?php

use Claudsonm\CepPromise\CepPromise;

require 'vendor/autoload.php';
@class_alias('dump', 'dd');
header('Content-Type: application/json');

echo CepPromise::find('49075-440');