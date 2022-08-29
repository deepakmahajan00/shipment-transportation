<?php

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

class LoadShipment extends BaseLoadData
{
  public const FIRST_HUNDRED_RATE = 0.30;
  public const SECOND_HUNDRED_RATE = 0.25;
  public const THIRD_HUNDRED_RATE = 0.20;
  public const FOURTH_HUNDRED_RATE = 0.15;

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
      $values[] = "(".$row['shipmentId'].", ".$row['distance'].", ".$row['time'].", ".$row['price'].", ".$companyId.", ".$carrierId.")";
    }
    $sql = "INSERT INTO `shipments` (`id`, `distance`, `time`, `price` , `company_id`, `carrier_id`) VALUES " . implode(',', $values);
    $this->conn->query($sql);
    $this->output->writeln("Total number of shipments inserted ".count($values));
  }

  public static function constructShipment(array $shipmentRow, array &$shipments): void
  {
    $shipment['shipmentId'] = $shipmentRow['id'];
    $shipment['distance'] = $shipmentRow['distance'];;
    $shipment['time'] = $shipmentRow['time'];;
    $shipment['price'] = self::calculateShipmentPricePer($shipment['distance']);
    $shipment['company_id'] = $shipmentRow['company']['email'];
    $shipment['carrier_id'] = $shipmentRow['carrier']['email'];
    $shipments['shipments'][$shipmentRow['id']] = $shipment;
  }

  private static function calculateShipmentPricePer(int $distance): float|int
  {
    $distanceInKM = floor($distance / 1000);
    if ($distanceInKM <= 100)
    {
      return $distanceInKM * self::FIRST_HUNDRED_RATE;
    }

    if ($distanceInKM <= 200)
    {
      return (100 * self::FIRST_HUNDRED_RATE) +
        ($distanceInKM - 100) * self::SECOND_HUNDRED_RATE;
    }

    if ($distanceInKM <= 300)
    {
      return (100 * self::FIRST_HUNDRED_RATE) +
        (100 * self::SECOND_HUNDRED_RATE) +
        ($distanceInKM - 200) * self::THIRD_HUNDRED_RATE;
    }

    if ($distanceInKM > 300)
    {
      return (100 * self::FIRST_HUNDRED_RATE) +
        (100 * self::SECOND_HUNDRED_RATE) +
        (100 * self::THIRD_HUNDRED_RATE) +
        ($distanceInKM - 300) * self::FOURTH_HUNDRED_RATE;
    }
    return 0;
  }
}