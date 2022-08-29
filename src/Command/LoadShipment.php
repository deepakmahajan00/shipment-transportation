<?php

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

class LoadShipment extends BaseLoadData
{
  public function run($conn, OutputInterface $output, array &$shipments, ?array $input = [])
  {
    $this->conn = $conn;
    $this->output = $output;
    $this->insertShipments($shipments['shipments'], $conn, $output);
  }

  protected function insertShipments(array $shipments, $conn, $output): void
  {
    $company = new LoadCompany();
    $company->conn = $conn;
    $companies = $company->getCompanies();

    $carrier = new LoadCarrier();
    $carrier->conn = $conn;
    $carriers = $carrier->getCarriers();

    $values = [];
    foreach($shipments as $row) {
      $companyId = $companies[$row['company_id']];
      $carrierId = $carriers[$row['carrier_id']];
      $values[] = "(".$row['shipmentId'].", ".$row['distance'].", ".$row['time'].", ".$companyId.", ".$carrierId.")";
    }
    $sql = "INSERT INTO `shipments` (`id`, `distance`, `time`, `company_id`, `carrier_id`) VALUES " . implode(',', $values);
    $this->conn->query($sql);
    $this->output->writeln("Total number of shipments inserted ".count($values));
  }

  public static function constructShipment(array $shipmentRow, array &$shipments): void
  {
    $shipment['shipmentId'] = $shipmentRow['id'];
    $shipment['distance'] = $shipmentRow['distance'];;
    $shipment['time'] = $shipmentRow['time'];;
    $shipment['price'] = 0.00;
    $shipment['company_id'] = $shipmentRow['company']['email'];
    $shipment['carrier_id'] = $shipmentRow['carrier']['email'];
    $shipments['shipments'][$shipmentRow['id']] = $shipment;
  }
}