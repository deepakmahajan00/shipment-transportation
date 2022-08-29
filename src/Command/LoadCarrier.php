<?php

namespace App\Command;

class LoadCarrier extends BaseLoadData
{
  public static function insertCarriers(): void
  {
    foreach(self::$carriers as $email => $row) {
      $compName = $row['name'];
      $values[] = "('".$email."', '".$compName."')";
    }
    $query = "INSERT INTO `carriers` (`email`, `name`) VALUES " . implode(',', $values);
    self::$conn->query($query);
    self::$output->writeln("Total number of carriers inserted ".count(self::$carriers));
  }

  public static function getCarriers(): array
  {
    $result = self::$conn->query("SELECT * FROM `carriers`");
    $companies = $result->fetch_all(MYSQLI_ASSOC);
    $result->free_result();
    return array_column($companies, 'id', 'email');
  }

  public static function constructCarrier(int $shipmentId, array $carrier): void
  {
    if (!array_key_exists($carrier['email'], self::$carriers))
    {
      self::$carriers[$carrier['email']]['name'] = $carrier['name'];
      self::$carriers[$carrier['email']]['shipment_id'] = $shipmentId;
    }
  }
}