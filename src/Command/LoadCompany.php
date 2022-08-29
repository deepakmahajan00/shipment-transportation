<?php

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

class LoadCompany extends BaseLoadData
{

  public function run($conn, OutputInterface $output, array &$shipments, ?array $input = [])
  {
    $this->conn = $conn;
    $this->output = $output;
    $this->insertCompanies($shipments['companies']);
  }

  public function insertCompanies(array $companies): void
  {
    $values = [];
    foreach($companies as $email => $row) {
      $compName = $row['name'];
      $values[] = "('".$email."', '".$compName."')";
    }
    $query = "INSERT INTO `companies` (`email`, `name`) VALUES " . implode(',', $values);
    $this->conn->query($query);
    $this->output->writeln("Total number of companies inserted ".count($companies));
  }

  public function getCompanies(): array
  {
    $result = $this->conn->query("SELECT * FROM `companies`");
    $companies = $result->fetch_all(MYSQLI_ASSOC);
    $result->free_result();
    return array_column($companies, 'id', 'email');
  }

  public static function constructCompany(int $shipmentId, array $company, array &$shipments): void
  {
    if (!isset($shipments['companies']) || !array_key_exists($company['email'], $shipments['companies']))
    {
      $shipments['companies'][$company['email']]['name'] = $company['name'];
      $shipments['companies'][$company['email']]['shipment_id'] = $shipmentId;
    }
  }
}