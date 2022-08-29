<?php

namespace App\Command;

class LoadShipmentStop extends BaseLoadData
{
  protected static function insertShipmentStops(): void
  {
    $values = [];
    $count = 0;
    foreach(self::$shipmentStops as $shipmentId => $stops) {
      $startId = self::$locationIdAndPostcodeMapping[$stops['start_postcode']];
      $stopId = self::$locationIdAndPostcodeMapping[$stops['stop_postcode']];
      $values[] = "(".$startId.", ".$stopId.", ".$shipmentId.")";
    }

    $sql = "INSERT INTO `shipments_stops` (`start_id`, `stop_id`, `shipment_id`) VALUES " . implode(',', $values);
    self::$conn->query($sql);
    self::$output->writeln("Total number of shipment stops inserted ".count(self::$shipmentStops));
  }
}