<?php

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

class LoadCarrier extends BaseLoadData
{
  public function run($conn, OutputInterface $output, array &$shipments, ?array $input = [])
  {
    $this->conn = $conn;
    $this->output = $output;
    $this->insertCarriers($shipments['carriers']);
  }

  public function insertCarriers(array $carriers): void
  {
    foreach($carriers as $email => $row) {
      $compName = $row['name'];
      $values[] = "('".$email."', '".$compName."')";
    }
    $query = "INSERT INTO `carriers` (`email`, `name`) VALUES " . implode(',', $values);
    $this->conn->query($query);
    $this->output->writeln("Total number of carriers inserted ".count($carriers));
  }

  public function getCarriers(): array
  {
    $result = $this->conn->query("SELECT * FROM `carriers`");
    $companies = $result->fetch_all(MYSQLI_ASSOC);
    $result->free_result();
    return array_column($companies, 'id', 'email');
  }

  public static function constructCarrier(int $shipmentId, array $carrier, array &$shipments): void
  {
    if (!isset($shipments['carriers']) || !array_key_exists($carrier['email'], $shipments['carriers']))
    {
      $shipments['carriers'][$carrier['email']]['name'] = $carrier['name'];
      $shipments['carriers'][$carrier['email']]['shipment_id'] = $shipmentId;
    }
  }
}