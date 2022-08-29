<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseLoadData
{
  protected OutputInterface $output;
  protected $conn;
  protected array $locationIdAndPostcodeMapping = [];
  protected array $shipments = [];

  abstract public function run($conn, OutputInterface $output, array &$finalShipments, ?array $input = []);

  private function fetchDataFromFile(): void
  {
    $filename = __DIR__ ."/../../infrastructure/shipments.json";
    $data = file_get_contents($filename);
    $this->shipmentsDataFromFile = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
  }

  protected function constructDataForLoad(): void
  {
    $this->fetchDataFromFile();

    // Extracting row by row
    foreach($this->shipmentsDataFromFile as $row) {
      $shipmentId = $row['id'];
      LoadShipment::constructShipment($row, $this->shipments);
      LoadCompany::constructCompany($shipmentId, $row['company'], $this->shipments);
      LoadCarrier::constructCarrier($shipmentId, $row['carrier'], $this->shipments);
      LoadLocation::constructLocation($shipmentId, $row['route'], $this->shipments);
    }
  }
}