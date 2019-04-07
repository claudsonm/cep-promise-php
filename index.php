<?php

use Claudsonm\CepPromise\CepPromise;
use Claudsonm\CepPromise\Exceptions\CepPromiseException;

require 'vendor/autoload.php';
@class_alias('dump', 'dd');
header('Content-Type: application/json');

try {
    $response = CepPromise::fetch(49040610);
} catch (CepPromiseException $e) {
    $response = $e->toArray();
}
echo json_encode($response);
