<?php
require(__DIR__ . "/../vendor/autoload.php");
$openapi = \OpenApi\Generator::scan([__DIR__ . '/../src/Controller']);
header('Content-Type: application/json');
echo $openapi->toJson();