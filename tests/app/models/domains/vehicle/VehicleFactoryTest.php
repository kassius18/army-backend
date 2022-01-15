<?php

use app\models\domains\vehicle\VehicleEntity;
use app\models\domains\vehicle\VehicleFactory;
use PHPUnit\Framework\TestCase;

class VehicleFactoryTest extends TestCase
{

  private static array $dbRecord;
  private static array $userPostInput;

  private VehicleEntity $vehicle;
  private VehicleEntity $secondVehicle;
  private VehicleEntity $vehicleWithoutId;

  public static function setUpBeforeClass(): void
  {
    self::$dbRecord = [
      [
        "id" => 1,
        "plate" => "plate1",
        "vehicle_type" => "vehicle1"
      ], [
        "id" => 2,
        "plate" => "plate2",
        "vehicle_type" => "vehicle2"
      ]
    ];

    self::$userPostInput = [
      "plate" => "plate3",
      "vehicleType" => "vehicle3"
    ];
  }

  protected function setUp(): void
  {
    $this->vehicle = new VehicleEntity(
      self::$dbRecord[0]['plate'],
      self::$dbRecord[0]['vehicle_type'],
      self::$dbRecord[0]['id'],
    );

    $this->secondVehicle = new VehicleEntity(
      self::$dbRecord[1]['plate'],
      self::$dbRecord[1]['vehicle_type'],
      self::$dbRecord[1]['id'],

    );

    $this->vehicleWithoutId = new VehicleEntity(
      self::$userPostInput['plate'],
      self::$userPostInput['vehicleType'],
    );
  }

  public function testCreatingVehicleFromDatabaseRecord()
  {
    $vehicleFromRecord = VehicleFactory::createVehicleFromRecord(self::$dbRecord[0]);
    $this->assertEquals($this->vehicle, $vehicleFromRecord);
  }

  public function testCreatingRequestFromUserInput()
  {
    $vehicleFromPost = VehicleFactory::createVehicleFromPost(self::$userPostInput);
    $this->assertEquals($this->vehicleWithoutId, $vehicleFromPost);
  }

  public function testCreatingManyVehiclesFromDatabaseRecord()
  {
    $expected = [$this->vehicle, $this->secondVehicle];
    $manyVehiclesFromRecord = VehicleFactory::createManyVehiclesFromRecord(self::$dbRecord);
    $this->assertEquals($expected, $manyVehiclesFromRecord);
  }
}
