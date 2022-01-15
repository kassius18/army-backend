<?php

use app\models\domains\vehicle\VehicleEntity;
use PHPUnit\Framework\TestCase;

class VehicleEntityTest extends TestCase
{
  private VehicleEntity $vehicleEntityWithId;
  private VehicleEntity $vehicleEntityWithoutId;
  private string $plate;
  private string $vehicleType;
  private int $id;

  public function setUp(): void
  {
    $this->plate = "somePlate";
    $this->vehicleType = "someVehcile";
    $this->id = 3;
    $this->vehicleEntityWithId = new VehicleEntity($this->plate, $this->vehicleType, $this->id);
    $this->vehicleEntityWithoutId = new VehicleEntity($this->plate, $this->vehicleType);
  }

  public function testEntityStructure()
  {
    $this->assertEquals($this->plate, $this->vehicleEntityWithId->getPlate());
    $this->assertEquals($this->vehicleType, $this->vehicleEntityWithId->getVehicleType());
    $this->assertEquals($this->id, $this->vehicleEntityWithId->getId());
  }

  public function testSerializingToJsonWithIdSet()
  {
    $expected = json_encode([
      "id" => $this->id,
      "plate" => $this->plate,
      "vehicleType" => $this->vehicleType
    ]);

    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($this->vehicleEntityWithId)
    );
  }

  public function testSerializingToJsonWithIdNotSet()
  {
    $expected = json_encode([
      "plate" => $this->plate,
      "vehicleType" => $this->vehicleType
    ]);

    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($this->vehicleEntityWithoutId)
    );
  }
}
