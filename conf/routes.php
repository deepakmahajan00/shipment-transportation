<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app)
{
  $app->get('/', function (Request $request, Response $response) {
    $response
      ->getBody()
      ->write('<h1>Welcome to Shipments API.</h1><br> Click here : <a href="http://0.0.0.0:18181/swagger/" target="_blank">Swagger API Doc</a>');
    $response->withStatus(200);
    return $response;
  });
  $app->group('/api/v1/shipments', function (Group $group)
  {
    $group->get('', 'App\Controller\ShipmentController:shipments')->setName('shipments');
    $group->get('/company/{name}', 'App\Controller\ShipmentController:getShipmentByCompany')->setName('getShipmentByCompany');
    $group->get('/carrier/{name}', 'App\Controller\ShipmentController:getShipmentByCarrier')->setName('getShipmentByCarrier');
    $group->get('/stop/{name}', 'App\Controller\ShipmentController:getShipmentByStop')->setName('getShipmentByStop');
  });
};
