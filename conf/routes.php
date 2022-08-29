<?php

declare(strict_types=1);

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app)
{
  $app->group('/shipments', function (Group $group)
  {
    $group->get('', 'App\Controller\ShipmentController:shipments')->setName('shipments');
    $group->get('/company/{name}', 'App\Controller\ShipmentController:getShipmentByCompany')->setName('getShipmentByCompany');
    $group->get('/carrier/{name}', 'App\Controller\ShipmentController:getShipmentByCarrier')->setName('getShipmentByCarrier');
    $group->get('/stop/{name}', 'App\Controller\ShipmentController:getShipmentByStop')->setName('getShipmentByStop');
  });
};
