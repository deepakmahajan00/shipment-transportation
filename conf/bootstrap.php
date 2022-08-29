<?php

use DI\ContainerBuilder;
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// Add DI container definitions
$containerBuilder->addDefinitions(__DIR__ . '/container.php');

// Set up dependencies
(require __DIR__ . '/dependencies.php')($containerBuilder);

// Create DI container instance
$container = $containerBuilder->build();


// Create Slim App instance
$app = $container->get(App::class);

// Register routes
(require __DIR__ . '/routes.php')($app);

return $app;