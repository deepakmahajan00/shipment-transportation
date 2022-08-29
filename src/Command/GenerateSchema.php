<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;

class GenerateSchema extends BaseLoadData
{


  private static function getShipmentsDropTable(): string
  {
    return "DROP TABLE IF EXISTS shipments";
  }

  private static function getShipmentsCreateTable(): string
  {
    return "CREATE TABLE `shipments` (
        `id` INT(10) UNSIGNED NOT NULL,
        `distance` INT(10) UNSIGNED NOT NULL DEFAULT 0,
        `time` INT(2) UNSIGNED NOT NULL,
        `price` DOUBLE(10,2) NOT NULL DEFAULT 0.00,
        `company_id` INT(5) UNSIGNED NOT NULL,
        `carrier_id` INT(5) UNSIGNED NOT NULL,
        PRIMARY KEY (`id`),
        KEY `IDX_COMPANY_ID` (`company_id`),
        KEY `IDX_CARRIER_ID` (`carrier_id`) -- ,
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
  }

  private static function getShipmentsStopsDropTable(): string
  {
    return "DROP TABLE IF EXISTS shipments_stops;";
  }

  private static function getShipmentsStopsCreateTable(): string
  {
    return "CREATE TABLE `shipments_stops` (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `shipment_id` INT(10) UNSIGNED NOT NULL,
        `start_id` INT(10) UNSIGNED NOT NULL,
        `stop_id` INT(10) UNSIGNED NOT NULL,
        PRIMARY KEY (`id`),
        KEY `IDX_SHIPMENT_ID` (`shipment_id`),
        KEY `IDX_START_ID` (`start_id`),
        KEY `IDX_STOP_ID` (`stop_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
  }

  private static function getLocationsDropTable(): string
  {
    return "DROP TABLE IF EXISTS locations;";
  }

  private static function getLocationsCreateTable(): string
  {
    return "CREATE TABLE `locations` (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `postcode` INT(6) UNSIGNED NOT NULL,
        `city` VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci,
        `country` VARCHAR(2) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `IDX_POSTCODE_ID` (`postcode`),
        KEY `IDX_CITY_ID` (`city`),
        UNIQUE KEY `IDX_POSTCODE_CITY_COUNTRY` (`postcode`, `city`, `country`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
  }

  private static function getCompaniesDropTable(): string
  {
    return "DROP TABLE IF EXISTS companies;";
  }

  private static function getCompaniesCreateTable(): string
  {
    return "CREATE TABLE `companies` (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci,
        `email` VARCHAR(80) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `IDX_NAME_ID` (`name`),
        KEY `IDX_EMAIL_ID` (`email`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
  }

  private static function getCarriersDropTable(): string
  {
    return "DROP TABLE IF EXISTS carriers;";
  }

  private static function getCarriersCreateTable(): string
  {
    return "CREATE TABLE `carriers` (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci,
        `email` VARCHAR(80) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `IDX_NAME_ID` (`name`),
        KEY `IDX_EMAIL_ID` (`email`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
  }

  protected static function getTableSchemas(): array
  {
    return [
      "DropShipmentsStopsTable" => self::getShipmentsStopsDropTable(),
      "CreateShipmentsStopsTable" => self::getShipmentsStopsCreateTable(),
      "DropLocationsTable" => self::getLocationsDropTable(),
      "CreateLocationsTable" => self::getLocationsCreateTable(),
      "DropShipmentsTable" => self::getShipmentsDropTable(),
      "CreateShipmentsTable" => self::getShipmentsCreateTable(),
      "DropCompaniesTable" => self::getCompaniesDropTable(),
      "CreateCompaniesTable" => self::getCompaniesCreateTable(),
      "DropCarrierTable" => self::getCarriersDropTable(),
      "CreateCarrierTable" => self::getCarriersCreateTable(),
    ];
  }
}