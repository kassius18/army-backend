<?php

use app\models\domains\vehicle\VehicleEntity;
use PHPUnit\Framework\TestCase;

class VehicleEntityTest extends TestCase
{
  private VehicleEntity $vehicleEntityWithId;
  private string $plate = "somePlate";
  private string $vehicleType = "someVehcile";
  private int $id = 333;

  public function setUp(): void
  {
    $this->vehicleEntity = new VehicleEntity($this->plate, $this->vehicleType, $this->id);
  }

  public function testEntityStructure()
  {
    $this->assertEquals($this->plate, $this->vehicleEntity->getPlate());
    $this->assertEquals($this->vehicleType, $this->vehicleEntity->getVehicleType());
    $this->assertEquals($this->id, $this->vehicleEntity->getId());
  }

  public function testSerializingToJson()
  {
    $expected = json_encode([
      "id" => $this->id,
      "plate" => $this->plate,
      "vehicleType" => $this->vehicleType
    ]);

    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($this->vehicleEntity)
    );
  }

  public function testSerializingToJsonWithNullInputs()
  {
    $expected = json_encode([
      "plate" => "",
      "vehicleType" => "",
      "id" => 3,
    ]);

    $actual = new VehicleEntity(null, null, 3);
    $this->assertJsonStringEqualsJsonString(
      $expected,
      json_encode($actual)
    );
  }
}
