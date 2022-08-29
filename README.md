# shipment-transportation
APIs for Shipment transportation

## Required technologies
1. PHP
2. Mysql
3. Docker
4. Swagger/Postman `to hit apis`

## Clone repository
git clone `https://github.com/deepakmahajan00/shipmentTransportation.git`

## Setup local without docker-compose
1. Clone repository
2. Change DB settings at `conf/settings.php`
3. Run `docker-compose`
4. Go to project directory and run `./bin/console.php app:init-db` to create db and load data to tables.
5. Run `php -S 0.0.0.0:18181 -t /public` to start server locallay
6. Go to browser and open `http://0.0.0.0:18181/swagger/` This will open api swagger documentation.

## Setup docker container
1. Clone repository
2. Run `docker-compose up`
3. Go to browser and open `http://0.0.0.0:18181/swagger/` This will open api swagger documentation.
