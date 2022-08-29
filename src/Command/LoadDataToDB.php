<?php

namespace App\Command;

class LoadDataToDB extends BaseLoadData
{
  public function loadData($conn, $output): void
  {
    self::$conn = $conn;
    self::$output = $output;
    $this->createSchema();
    $this->constructDataForLoad();

    $this->loadCompanies();
    $this->loadCarriers();
    $this->loadLocations();
    $this->loadShipmentStops();
    $this->loadShipments();
  }
}