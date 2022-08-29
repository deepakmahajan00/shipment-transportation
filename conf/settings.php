<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
  $rootPath = realpath(__DIR__ . '/..');

  // Global Settings Object
  $containerBuilder->addDefinitions([
    'settings' => [
      // Base path
      'base_path' => '',

      // Is debug mode
      'debug' => (getenv('APPLICATION_ENV') != 'production'),

      // 'Temprorary directory
      'temporary_path' => $rootPath . '/var/tmp',

      // Route cache
      'route_cache' =>$rootPath . '/var/cache/routes',

      // doctrine settings
      'doctrine' => [
        'dev_mode' => (getenv('APPLICATION_ENV') != 'production'),
        'meta' => [
          'entity_path' => [ $rootPath . '/src/Entity' ],
          'proxy_dir' => $rootPath . '/var/cache/proxies',
          'cache' => null,
        ],
        'cache_dir' => $rootPath . '/var/cache/doctrine',
        /*'connection' => [
          'driver' => 'pdo_mysql',
          'host' => '127.0.0.1',
          'port' => 13306,
          'dbname' => 'shipments',
          'user' => 'root',
          'password' => 'pole',
          'charset' => 'utf-8',
          'unix_socket' => '/var/run/mysqld/mysqld.sock',
        ]*/
        'connection' => [
          'driver' => 'pdo_mysql',
          'host' => 'mydb',
          'port' => 3306,
          'dbname' => 'shipments',
          'user' => 'root',
          'password' => 'pole',
          'charset' => 'utf-8',
          'unix_socket' => '/var/run/mysqld/mysqld.sock',
        ]
      ],

      // monolog settings
      'logger' => [
        'name' => 'app',
        'path' =>  getenv('docker') ? 'php://stdout' : $rootPath . '/var/log/app.log',
        'level' => (getenv('APPLICATION_ENV') != 'production') ? Logger::DEBUG : Logger::INFO,
      ]
    ],
  ]);

  if (getenv('APPLICATION_ENV') == 'production') { // Should be set to true in production
    $containerBuilder->enableCompilation($rootPath . '/var/cache');
  }
};
