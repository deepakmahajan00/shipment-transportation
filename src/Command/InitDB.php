<?php

namespace App\Command;

use DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDB extends Command
{
  private $settings;
  public function __construct(Container $container)
  {
    parent::__construct();
    $this->settings = $container->get('settings');
  }

  protected function configure()
  {
    $this
      // the name of the command (the part after "bin/console")
      ->setName('app:init-db')

      // the short description shown while running "php bin/console list"
      ->setDescription('Initialize database')

      // the full command description shown when running the command with
      // the "--help" option
      ->setHelp('Create database structe and add initial data');
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $dbConnection = new DbConnection($this->settings['doctrine']['connection'], $output);
    $conn = $dbConnection->getConnection();
    if ($conn === Command::INVALID)
    {
      return Command::INVALID;
    }

    (new LoadDataToDB())->loadData($conn, $output);
    $conn->close();
    return Command::SUCCESS;
  }
}
