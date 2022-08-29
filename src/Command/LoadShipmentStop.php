<?php

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

class LoadShipmentStop extends BaseLoadData
{
  public function run($conn, OutputInterface $output, array &$shipments, ?array $input = [])
  {
    $this->conn = $conn;
    $this->output = $output;
    $this->insertShipmentStops($shipments);
  }

  protected function insertShipmentStops(array &$shipments): void
  {
    $values = [];
    foreach($shipments['shipmentStops'] as $shipmentId => $stops) {
      $startId = $shipments['locationIdAndPostcodeMapping'][$stops['start_postcode']];
      $stopId = $shipments['locationIdAndPostcodeMapping'][$stops['stop_postcode']];
      $values[] = "(".$startId.", ".$stopId.", ".$shipmentId.")";
    }

    $sql = "INSERT INTO `shipments_stops` (`start_id`, `stop_id`, `shipment_id`) VALUES " . implode(',', $values);
    $this->conn->query($sql);
    $this->output->writeln("Total number of shipment stops inserted ".count($shipments['shipmentStops']));
  }
}