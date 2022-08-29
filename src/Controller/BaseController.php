<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;

abstract class BaseController
{
  protected $logger;

  /** @var \PDO */
  protected $em;  // Entities Manager

  public function __construct(ContainerInterface $container)
  {
    $this->logger = $container->get('logger');
    $this->em = $container->get('em');
  }
}