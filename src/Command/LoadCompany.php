<?php

namespace App\Command;

class LoadCompany extends BaseLoadData
{

  public static function insertCompanies(): void
  {
    $values = [];
    foreach(self::$companies as $email => $row) {
      $compName = $row['name'];
      $values[] = "('".$email."', '".$compName."')";
    }
    $query = "INSERT INTO `companies` (`email`, `name`) VALUES " . implode(',', $values);
    self::$conn->query($query);
    self::$output->writeln("Total number of companies inserted ".count(self::$companies));
  }

  public static function getCompanies(): array
  {
    $result = self::$conn->query("SELECT * FROM `companies`");
    $companies = $result->fetch_all(MYSQLI_ASSOC);
    $result->free_result();
    return array_column($companies, 'id', 'email');
  }

  public static function constructCompany(int $shipmentId, array $company): void
  {
    if (!array_key_exists($company['email'], self::$companies))
    {
      self::$companies[$company['email']]['name'] = $company['name'];
      self::$companies[$company['email']]['shipment_id'] = $shipmentId;
    }
  }
}