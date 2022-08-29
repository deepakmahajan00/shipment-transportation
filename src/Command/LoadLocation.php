<?php

namespace App\Command;

class LoadLocation extends BaseLoadData
{
  public static function insertLocations(): void
  {
    foreach(self::$locations as $row) {
      $postcode = $row['postcode'];
      $city = $row['city'];
      $country = $row['country'];

      $query = "INSERT INTO `locations` (`postcode`, `city`, `country`) VALUES ($postcode, '".$city."', '".$country."'); ";
      self::$conn->query($query);
      self::$locationIdAndPostcodeMapping[$postcode] = self::$conn->insert_id;
    }
    self::$output->writeln("Total number of locations inserted ".count(self::$locations));
  }

  protected static function constructLocation(int $shipmentId, array $route): void
  {
    if (is_array($route))
    {
      // Skip to add shipment details if any one of source or destination stops not exists
      $source = $route[0];
      $destination = $route[1];
      if (empty($source['postcode']) || empty($source['city']) || empty($destination['postcode']) || empty($destination['city']))
      {
        $skippedShipments[$shipmentId] = $route;
        return;
      }

      if (!array_key_exists($source['postcode'], self::$locations))
      {
        self::$locations[$source['postcode']] = $source;
      }
      if (!array_key_exists($destination['postcode'], self::$locations))
      {
        self::$locations[$destination['postcode']] = $destination;
      }

      // construct shipments
      self::$shipmentStops[$shipmentId] = [
        'start_postcode' => $source['postcode'],
        'stop_postcode' => $destination['postcode'],
      ];
    }
  }
}