<?php

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

class LoadDataToDB extends BaseLoadData
{

  public function run($conn, OutputInterface $output, array &$shipments, ?array $input = [])
  {
    (new GenerateSchema())->run($conn, $output, $shipments);
    $this->constructDataForLoad();
    (new LoadCompany())->run($conn, $output, $this->shipments);
    (new LoadCarrier())->run($conn, $output, $this->shipments);
    (new LoadLocation())->run($conn, $output, $this->shipments);
    (new LoadShipmentStop())->run($conn, $output, $this->shipments);
    (new LoadShipment())->run($conn, $output, $this->shipments);
  }

  public function loadData($conn, $output): void
  {
    $this->conn = $conn;
    $this->output = $output;

    $this->run($conn, $output, $this->shipments);
  }
}