<?php

namespace app\controllers;

use app\core\Response;
use app\core\Request;
use app\models\domains\vehicle\VehicleMapper;
use app\models\domains\vehicle\VehicleFactory;
use PDOException;

class VehicleController
{
  private Request $request;
  private VehicleMapper $vehicleMapper;
  private Response $response;

  public function __construct(Request $request, VehicleMapper $vehicleMapper, Response $response)
  {
    $this->request = $request;
    $this->vehicleMapper = $vehicleMapper;
    $this->response = $response;
  }

  public function handleGetRequest()
  {
    $allVehicles = $this->vehicleMapper->getAllVehicles();
    if ($allVehicles) {
      $this->response->setResponseBody(json_encode($allVehicles));
      $this->response->setStatusCode(200);
    } else {
      $this->response->setStatusCode(500);
    }
    $this->response->sendResponse();
  }

  public function handlePostRequest()
  {
    $vehicleAsArray = $this->request->getRequestBody();
    $vehicle = VehicleFactory::createVehicleFromInput($vehicleAsArray);
    try {
      $vehicle = $this->vehicleMapper->saveVehicle($vehicle);
      $this->response->setResponseBody(json_encode($vehicle));
      $this->response->setStatusCode(201);
    } catch (PDOException $e) {
      if ($e->getCode() === '23000') {
        $this->response->setStatusCode(409);
      } else {
        $this->response->setStatusCode(500);
      }
    }
    $this->response->sendResponse();
  }

  public function handlePutRequest()
  {
    $vehicleAsArray = $this->request->getRequestBody();
    $vehicle = VehicleFactory::createVehicleFromInput($vehicleAsArray);
    $vehicleId = json_decode($this->request->splitRequestUri()[1]);
    if (is_int($vehicleId)) {
      try {
        $vehicle = $this->vehicleMapper->updateVehicle($vehicle, $vehicleId);
        $this->response->setStatusCode(201);
        $this->response->setResponseBody(json_encode($vehicle));
      } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
          $this->response->setStatusCode(409);
        } else {
        }
        $this->response->setStatusCode(500);
      }
    }
    $this->response->sendResponse();
  }

  public function handleDeleteRequest()
  {
    $vehicleId = json_decode($this->request->splitRequestUri()[1]);
    if (is_int($vehicleId)) {
      try {
        if ($this->vehicleMapper->deleteVehicle($vehicleId)) {
          $this->response->setStatusCode(204);
        };
      } catch (PDOException $e) {
        $this->response->setStatusCode(500);
      }
    }
    $this->response->sendResponse();
  }
}
