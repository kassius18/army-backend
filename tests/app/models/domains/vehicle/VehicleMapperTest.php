<?php

use app\models\domains\vehicle\VehicleEntity;
use app\models\domains\vehicle\VehicleFactory;
use app\models\domains\vehicle\VehicleMapper;
use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class VehicleMapperTest extends TestCase
{
  private static ?PDO $pdo;
  private static PhinxApplication $phinxApp;
  private VehicleMapper $vehicleMapper;

  private VehicleEntity $vehicle;
  private VehicleEntity $secondVehicle;

  public static function setUpBeforeClass(): void
  {
    self::$pdo = include(TEST_DIR . "/setDatabaseForTestsScript.php");
    self::$phinxApp = new PhinxApplication();
  }

  public static function tearDownAfterClass(): void
  {
    self::$pdo = null;
  }

  protected function setUp(): void
  {
    self::$phinxApp->setAutoExit(false);
    self::$phinxApp->run(new StringInput('migrate -e testing'), new NullOutput());
    $this->vehicleMapper = new VehicleMapper(self::$pdo);

    $this->vehicle = new VehicleEntity("plate1", "vehicle1");
    $this->secondVehicle = new VehicleEntity("plate2", "vehicle2");
  }

  protected function tearDown(): void
  {
    self::$phinxApp->run(new StringInput('rollback -e testing -t 0'), new NullOutput());
  }

  public function testGettingAllVehiclesFromDb()
  {
    $sql = "INSERT INTO vehicle 
      (plate, vehicle_type)
      VALUES 
      ('plate1', 'vehicle1'),
      ('plate2', 'vehicle2')";
    self::$pdo->query($sql);

    $expected = [$this->vehicle, $this->secondVehicle];
    $actual = $this->vehicleMapper->getAllVehicles();
    $this->assertCount(2, $actual);
    $this->testTwoVehiclesAreEqualWithoutCheckingForId($expected[0], $actual[0]);
    $this->testTwoVehiclesAreEqualWithoutCheckingForId($expected[1], $actual[1]);
  }

  private function testTwoVehiclesAreEqualWithoutCheckingForId(VehicleEntity $firstEntry, VehicleEntity $secondEntry)
  {
    $firstEntryAsArray = json_decode(json_encode($firstEntry), true);
    $secondEntryAsArray = json_decode(json_encode($secondEntry), true);

    unset($firstEntryAsArray['id']);
    unset($secondEntryAsArray['id']);

    $this->assertEquals($firstEntryAsArray, $secondEntryAsArray);
  }

  public function testSavingVehicle()
  {
    $expected = new VehicleEntity("plate1", "vehicle2", 1);
    $this->vehicleMapper->saveVehicle($expected);

    $sql = "SELECT * FROM vehicle";
    $actual = VehicleFactory::createManyVehiclesFromRecord(self::$pdo->query($sql)->fetchAll());

    $this->assertEquals([$expected], $actual);
  }

  public function testDeletingVehicle()
  {
    $sql = "INSERT INTO vehicle 
      (plate, vehicle_type)
      VALUES 
      ('plate1', 'vehicle1')";
    self::$pdo->query($sql);

    $this->vehicleMapper->deleteVehicle(1);
    $actual = $this->vehicleMapper->getAllVehicles();
    $this->assertCount(0, $actual);
  }

  public function testEditingVehicle()
  {
    $sql = "INSERT INTO vehicle 
      (plate, vehicle_type)
      VALUES 
      ('plate1', 'vehicle1')";
    self::$pdo->query($sql);

    $secondVehicleWithId = new VehicleEntity("plate2", "vehicle2", 1);

    $this->vehicleMapper->updateVehicle(1, $secondVehicleWithId);
    $actual = $this->vehicleMapper->getAllVehicles();
    $this->assertCount(1, $actual);
    $this->assertEquals($secondVehicleWithId, $actual[0]);
  }

  /* TODO:
    * make the insertion into a private function  <15-01-22, yourname> */
}
