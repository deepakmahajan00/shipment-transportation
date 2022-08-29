<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

class DbConnection
{
  private array $connectionSettings;
  private OutputInterface $output;

  public function __construct($connectionSetting, OutputInterface $output)
  {
    $this->connectionSettings = $connectionSetting;
    $this->output = $output;
  }

  public function getConnection(): int|\Mysqli
  {
    $connectionDetails = $this->connectionSettings;
    $this->conn = new \Mysqli(
      $connectionDetails['host'],
      $connectionDetails['user'],
      $connectionDetails['password'],
      $connectionDetails['dbname'],
      $connectionDetails['port']
    );

    if (!$this->conn)
    {
      $this->output->writeLn('Can\'t open mysqli database ');
      return Command::INVALID;
    }
    return $this->conn;
  }
}