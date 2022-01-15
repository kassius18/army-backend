<?php

namespace app\models\domains\vehicle;

use JsonSerializable;

class VehicleEntity implements JsonSerializable
{
  private ?int $id;
  private string $plate;
  private string $vehicleType;


  public function __construct($plate, $vehicleType, $id = null)
  {
    $this->id = $id;
    $this->plate = $plate;
    $this->vehicleType = $vehicleType;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getVehicleType()
  {
    return $this->vehicleType;
  }

  public function getPlate()
  {
    return $this->plate;
  }

  public function jsonSerialize(): array
  {
    $arrayWithoutId = [
      "plate" => $this->plate,
      "vehicleType" => $this->vehicleType,
    ];

    if (isset($this->id)) {
      $arrayWithoutId["id"] = $this->id;
    }

    return $arrayWithoutId;
  }
}
