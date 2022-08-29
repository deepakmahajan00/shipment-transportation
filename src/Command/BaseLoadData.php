<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseLoadData
{
  protected static OutputInterface $output;
  protected static $conn;
  protected static array $shipmentsDataFromFile = [];
  protected static array $shipments = [];
  protected static array $locations = [];
  protected static array $shipmentStops = [];
  protected static array $companies = [];
  protected static array $carriers = [];
  protected static array $locationIdAndPostcodeMapping = [];

  private function fetchDataFromFile(): void
  {
    $filename = __DIR__ ."/../../infrastructure/shipments.json";
    $data = file_get_contents($filename);
    self::$shipmentsDataFromFile = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
  }

  public function createSchema(): void
  {
    $queries = GenerateSchema::getTableSchemas();
    try
    {
      self::$conn->begin_transaction();
      foreach($queries as $tableName => $query) {
        if (!self::$conn->query($query)) {
          self::$output->writeLn('Can\'t execute SQL query "' . $query . '": ' . self::$conn->error);
        } else {
          self::$output->writeLn($tableName);
        }
      }
      self::$conn->commit();
    } catch (\Exception $e)
    {
      self::$conn->rollback();
      throw new \Exception($e->getMessage());
    }
    self::$output->writeLn("Database structure created");
  }



  /*private function constructCarrier(int $shipmentId, array $carrier): void
  {
    if (!array_key_exists($carrier['email'], self::$carriers))
    {
      self::$carriers[$carrier['email']]['name'] = $carrier['name'];
      self::$carriers[$carrier['email']]['shipment_id'] = $shipmentId;
    }
  }*/

  /*private function constructCompany(int $shipmentId, array $company): void
  {
    if (!array_key_exists($company['email'], self::$companies))
    {
      self::$companies[$company['email']]['name'] = $company['name'];
      self::$companies[$company['email']]['shipment_id'] = $shipmentId;
    }
  }*/

  /*private function constructLocation(int $shipmentId, array $route): void
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
  }*/

  /*private function constructShipment(array $shipmentRow): void
  {
    $shipment['shipmentId'] = $shipmentRow['id'];
    $shipment['distance'] = $shipmentRow['distance'];;
    $shipment['time'] = $shipmentRow['time'];;
    $shipment['price'] = 0.00;
    $shipment['company_id'] = $shipmentRow['company']['email'];
    $shipment['carrier_id'] = $shipmentRow['carrier']['email'];
    self::$shipments[$shipmentRow['id']] = $shipment;
  }*/

  protected function constructDataForLoad(): void
  {
    $this->fetchDataFromFile();

    // Extracting row by row
    foreach(self::$shipmentsDataFromFile as $row) {
      $shipmentId = $row['id'];
      LoadShipment::constructShipment($row);
      LoadCompany::constructCompany($shipmentId, $row['company']);
      LoadCarrier::constructCarrier($shipmentId, $row['carrier']);
      LoadLocation::constructLocation($shipmentId, $row['route']);
    }
  }

  protected function loadCompanies(): void
  {
    LoadCompany::insertCompanies();
  }

  protected function loadCarriers(): void
  {
    LoadCarrier::insertCarriers();
  }

  protected function loadLocations(): void
  {
    LoadLocation::insertLocations();
  }

  protected function loadShipmentStops(): void
  {
    LoadShipmentStop::insertShipmentStops();
  }

  protected function loadShipments(): void
  {
    LoadShipment::insertShipments();
  }
}