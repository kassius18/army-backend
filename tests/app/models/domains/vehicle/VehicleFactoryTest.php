<?php

use app\models\domains\vehicle\VehicleEntity;
use app\models\domains\vehicle\VehicleFactory;
use PHPUnit\Framework\TestCase;

class VehicleFactoryTest extends TestCase
{

  private static array $dbRecord;
  private static array $userInput;

  private VehicleEntity $vehicle;
  private VehicleEntity $secondVehicle;
  private VehicleEntity $vehicleFromUserInput;
  private VehicleEntity $vehicleWithNullValues;
  private VehicleEntity $vehicleWithEmptyValues;

  public static function setUpBeforeClass(): void
  {
    self::$dbRecord = [
      [
        "vehicle_id" => 1,
        "plate" => "plate1",
        "vehicle_type" => "vehicle1"
      ], [
        "vehicle_id" => 2,
        "plate" => "plate2",
        "vehicle_type" => "vehicle2"
      ], [
        "vehicle_id" => 3,
        "plate" => null,
        "vehicle_type" => null
      ]
    ];

    self::$userInput = [
      [
        "id" => 4,
        "plate" => "plate3",
        "vehicleType" => "vehicle3"
      ], [
        "id" => 5,
        "plate" => "",
        "vehicleType" => ""

      ]
    ];
  }

  protected function setUp(): void
  {
    $this->vehicle = new VehicleEntity(
      self::$dbRecord[0]["plate"],
      self::$dbRecord[0]["vehicle_type"],
      self::$dbRecord[0]["vehicle_id"],
    );

    $this->secondVehicle = new VehicleEntity(
      self::$dbRecord[1]["plate"],
      self::$dbRecord[1]["vehicle_type"],
      self::$dbRecord[1]["vehicle_id"],

    );

    $this->vehicleWithNullValues = new VehicleEntity(
      self::$dbRecord[2]["plate"],
      self::$dbRecord[2]["vehicle_type"],
      self::$dbRecord[2]["vehicle_id"],

    );

    $this->vehicleFromUserInput = new VehicleEntity(
      self::$userInput[0]["plate"],
      self::$userInput[0]["vehicleType"],
      self::$userInput[0]["id"],
    );

    $this->vehicleWithEmptyValues = new VehicleEntity(
      self::$userInput[1]["plate"],
      self::$userInput[1]["vehicleType"],
      self::$userInput[1]["id"],
    );
  }

  public function testCreatingVehicleFromDatabaseRecord()
  {
    $vehicleFromRecord = VehicleFactory::createVehicleFromRecord(self::$dbRecord[0]);
    $this->assertJsonStringEqualsJsonString(json_encode($this->vehicle), json_encode($vehicleFromRecord));
  }

  public function testCreatingRequestFromUserInput()
  {
    $vehicleFromInput = VehicleFactory::createVehicleFromInput(self::$userInput[0]);
    $this->assertJsonStringEqualsJsonString(
      json_encode($this->vehicleFromUserInput),
      json_encode($vehicleFromInput)
    );
  }

  public function testCreatingManyVehiclesFromDatabaseRecord()
  {
    $expected = [$this->vehicle, $this->secondVehicle, $this->vehicleWithNullValues];
    $vehiclesFromRecord = VehicleFactory::createManyVehiclesFromRecord(self::$dbRecord);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($vehiclesFromRecord));
  }

  public function testCreatingArrayFromRecordWithEmptyValues()
  {
    $expected = $this->vehicleWithNullValues;
    $vehicleFromRecordWithEmptyValues = VehicleFactory::createVehicleFromRecord(self::$dbRecord[2]);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($vehicleFromRecordWithEmptyValues));
  }

  public function testCreatingArrayFromUserInputWithEmptyValues()
  {
    $expected = $this->vehicleWithEmptyValues;
    $vehiclesFromRecordWithEmptyValues = VehicleFactory::createVehicleFromInput(self::$userInput[1]);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($vehiclesFromRecordWithEmptyValues));
  }
}
