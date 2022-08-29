<?php

namespace App\Controller;

use OpenApi\Annotations as OA;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @OA\Info(title="Shipments API", version="0.1")
 */
class ShipmentController extends BaseController
{
  private function getSql()
  {
    return 'select 
        s.id, s.distance, s.time, s.price
          , cp.name, cp.email
          , cr.name, cr.email
          , ss.start_id, ss.stop_id
          , l.postcode start_postcode, l.city start_city, l.country start_country
          , ll.postcode stop_postcode, ll.city stop_city, ll.country stop_country
      from shipments as s 
      LEFT JOIN companies cp ON cp.id = s.company_id
      LEFT JOIN carriers cr ON cr.id = s.carrier_id
      LEFT JOIN shipments_stops ss ON ss.shipment_id = s.id
      LEFT JOIN locations l ON l.id = ss.start_id
      LEFT JOIN locations ll ON ll.id = ss.stop_id';
  }

  private function handleSqlExecution(Response $response, string $sql): void
  {
    try {
      $shipments = $this->em->query($sql)->fetchAll();
      $response
        ->getBody()
        ->write(json_encode([
          'total_records' => count($shipments),
          'shipments' => $shipments
        ], JSON_THROW_ON_ERROR));
      $response->withStatus(200);
    } catch (PDOException $e) {
      $error = array(
        "message" => $e->getMessage(),
        "error" => $e->getCode(),
      );
      $response->getBody()->write(json_encode($error, JSON_THROW_ON_ERROR));
      $response->withStatus(500);
    }
  }

  /**
   *  @OA\Get(path="/api/v1/shipments", tags={"shipments"},consumes={"application/json"}, produces={"application/json"},
   *  @OA\Response(response="200", description="Success"),
   *  @OA\Response(response="404", description="Not Found")
   * )
   */
  public function shipments(Request $request, Response $response): Response
  {
    $this->handleSqlExecution($response, $this->getSql());
    return $response->withHeader('content-type', 'application/json; charset=utf-8');
  }

  /**
   * @OA\Get(path="/api/v1/shipments/company/{name}", tags={"shipments"}, consumes={"application/json"}, produces={"application/json"},
   * @OA\Parameter(
   *         name="name",
   *         in="path",
   *         description="company name",
   *         example="Laurine Bartoletti",
   *         required=true,
   *         type="string"
   *   ),
   *  @OA\Response(response="200", description="Success"),
   *  @OA\Response(response="404", description="Not Found")
   * )
   */
  public function getShipmentByCompany(Request $request, Response $response, array $args = []): Response
  {
    $name = $args['name'];
    $where = " WHERE cp.name LIKE '%$name%'";
    $sql = $this->getSql() . $where;
    $this->handleSqlExecution($response, $sql);
    return $response->withHeader('content-type', 'application/json; charset=utf-8');
  }

  /**
   * @OA\Get(path="/api/v1/shipments/carrier/{name}", tags={"shipments"}, consumes={"application/json"}, produces={"application/json"},
   * @OA\Parameter(
   *         name="name",
   *         in="path",
   *         description="carrier name",
   *         example="Luisa Shields",
   *         required=true,
   *         type="string"
   *   ),
   *  @OA\Response(response="200", description="Success"),
   *  @OA\Response(response="404", description="Not Found")
   * )
   */
  public function getShipmentByCarrier(Request $request, Response $response, array $args = []): Response
  {
    $name = $args['name'];
    $where = " WHERE cr.name LIKE '%$name%'";
    $sql = $this->getSql() . $where;
    $this->handleSqlExecution($response, $sql);
    return $response->withHeader('content-type', 'application/json; charset=utf-8');
  }

  /**
   * @OA\Get(path="/api/v1/shipments/stop/{name}", tags={"shipments"}, consumes={"application/json"}, produces={"application/json"},
   *  @OA\Parameter(
   *    name="name",
   *    in="path",
   *    description="city name",
   *    required="true",
   *    type="string",
   *    example="wilnsdorf"
   *   ),
   *  @OA\Response(response="200", description="Success"),
   *  @OA\Response(response="404", description="Not Found")
   * )
   */
  public function getShipmentByStop(Request $request, Response $response, array $args = []): Response
  {
    $name = $args['name'];
    $where = " WHERE l.city LIKE '%$name%' OR ll.city LIKE '%$name%'";

    $sql = $this->getSql() . $where;
    $this->handleSqlExecution($response, $sql);
    return $response->withHeader('content-type', 'application/json; charset=utf-8');
  }
}