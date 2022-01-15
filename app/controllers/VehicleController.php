<?php

namespace app\controllers;

use app\core\Request;
use app\models\domains\vehicle\VehicleMapper;
use app\models\domains\vehicle\VehicleFactory;

use function PHPSTORM_META\type;

class VehicleController
{
  private Request $request;
  private VehicleMapper $vehicleMapper;

  public function __construct(Request $request, VehicleMapper $vehicleMapper)
  {
    $this->request = $request;
    $this->vehicleMapper = $vehicleMapper;
  }

  public function handleGetRequest()
  {
    $allVehicles = $this->vehicleMapper->getAllVehicles();
    echo (json_encode($allVehicles));
  }

  public function handlePostRequest()
  {
    $userPostInput = $this->request->getRequestBody();
    $vehicleEntity = VehicleFactory::createVehicleFromPost($userPostInput);
    $this->vehicleMapper->saveVehicle($vehicleEntity);
  }

  public function handlePutRequest()
  {
    $userPutInput = $this->request->getRequestBody();
    $vehicle = VehicleFactory::createVehicleFromPost($userPutInput);
    $vehicleId = json_decode($this->request->splitRequestUri()[1]);
    if (is_int($vehicleId)) {
      $this->vehicleMapper->updateVehicle($vehicleId, $vehicle);
    } else {
      var_dump("not found");
    }
  }

  public function handleDeleteRequest()
  {
    $vehicleId = json_decode($this->request->splitRequestUri()[1]);
    if (is_int($vehicleId)) {
      $this->vehicleMapper->deleteVehicle($vehicleId);
    } else {
      var_dump("not found");
    }
  }
}
