<?php

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

class LoadLocation extends BaseLoadData
{
  public function run($conn, OutputInterface $output, array &$shipments, ?array $input = [])
  {
    $this->conn = $conn;
    $this->output = $output;
    $this->insertLocations($shipments);
  }

  public function insertLocations(array &$shipments): void
  {
    foreach($shipments['locations'] as $row) {
      $postcode = $row['postcode'];
      $city = $row['city'];
      $country = $row['country'];

      $query = "INSERT INTO `locations` (`postcode`, `city`, `country`) VALUES ($postcode, '".$city."', '".$country."'); ";
      $this->conn->query($query);
      $this->locationIdAndPostcodeMapping[$postcode] = $this->conn->insert_id;
    }
    $shipments['locationIdAndPostcodeMapping'] = $this->locationIdAndPostcodeMapping;
    $this->output->writeln("Total number of locations inserted ".count($shipments['locations']));
  }

  public static function constructLocation(int $shipmentId, array $route, array &$shipments): void
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

      if (!isset($shipments['locations']) || !array_key_exists($source['postcode'], $shipments['locations']))
      {
        $shipments['locations'][$source['postcode']] = $source;
      }
      if (!isset($shipments['locations']) || !array_key_exists($destination['postcode'], $shipments['locations']))
      {
        $shipments['locations'][$destination['postcode']] = $destination;
      }

      // construct shipments
      $shipments['shipmentStops'][$shipmentId] = [
        'start_postcode' => $source['postcode'],
        'stop_postcode' => $destination['postcode'],
      ];
    }
  }
}