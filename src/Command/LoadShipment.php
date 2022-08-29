<?php

namespace App\Command;

class LoadShipment extends BaseLoadData
{
  protected static function insertShipments(): void
  {
    $companies = LoadCompany::getCompanies();
    $carriers = LoadCarrier::getCarriers();
    $values = [];
    foreach(self::$shipments as $row) {
      $companyId = $companies[$row['company_id']];
      $carrierId = $carriers[$row['carrier_id']];
      $values[] = "(".$row['shipmentId'].", ".$row['distance'].", ".$row['time'].", ".$companyId.", ".$carrierId.")";
    }
    $sql = "INSERT INTO `shipments` (`id`, `distance`, `time`, `company_id`, `carrier_id`) VALUES " . implode(',', $values);
    self::$conn->query($sql);
    self::$output->writeln("Total number of shipments inserted ".count($values));
  }

  protected static function constructShipment(array $shipmentRow): void
  {
    $shipment['shipmentId'] = $shipmentRow['id'];
    $shipment['distance'] = $shipmentRow['distance'];;
    $shipment['time'] = $shipmentRow['time'];;
    $shipment['price'] = 0.00;
    $shipment['company_id'] = $shipmentRow['company']['email'];
    $shipment['carrier_id'] = $shipmentRow['carrier']['email'];
    self::$shipments[$shipmentRow['id']] = $shipment;
  }
}