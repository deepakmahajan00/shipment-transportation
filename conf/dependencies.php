<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
      'logger' => function (ContainerInterface $container) {
        $settings = $container->get('settings');

        $loggerSettings = $settings['logger'];
        $logger = new Logger($loggerSettings['name']);

        $processor = new UidProcessor();
        $logger->pushProcessor($processor);

        $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
        $logger->pushHandler($handler);

        return $logger;
      },
      'em1' => function (ContainerInterface $container) {
        $settings = $container->get('settings');
        $config = ORMSetup::createAnnotationMetadataConfiguration(
          $settings['doctrine']['meta']['entity_path'],
          $settings['doctrine']['dev_mode'],
          $settings['doctrine']['meta']['proxy_dir'],
          $settings['doctrine']['meta']['cache'],
          false);

        return EntityManager::create($settings['doctrine']['connection'], $config);
      },
      "em" => function (ContainerInterface $c) {

        $settings = $c->get('settings');

        $dbSettings = $settings['doctrine']['connection'];

        $host = $dbSettings['host'];
        $dbname = $dbSettings['dbname'];
        $username = $dbSettings['user'];
        $password = $dbSettings['password'];
        $port = $dbSettings['port'];
        $options = [
          PDO::ATTR_EMULATE_PREPARES   => false, // turn off emulation mode for "real" prepared statements
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //make the default fetch be an associative array
        ];

        $dsn = "mysql:host=$host;dbname=$dbname;password=$password;port=$port";
        return new PDO($dsn, $username, $password, $options);
      },
    ]);
};

