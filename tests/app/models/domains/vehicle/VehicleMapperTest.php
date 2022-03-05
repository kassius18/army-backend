<?php

use app\models\domains\vehicle\VehicleMapper;
use common\MapperCommonMethods;
use fixtures\VehicleFixture;
use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class VehicleMapperTest extends TestCase
{
  private static ?PDO $pdo;
  private static PhinxApplication $phinxApp;
  private static VehicleFixture $fixture;
  private VehicleMapper $vehicleMapper;

  public static function setUpBeforeClass(): void
  {
    self::$pdo = include(TEST_DIR . "/setDatabaseForTestsScript.php");
    self::$phinxApp = new PhinxApplication();
    self::$fixture = new VehicleFixture(self::$pdo);
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
  }

  protected function tearDown(): void
  {
    self::$phinxApp->run(new StringInput('rollback -e testing -t 0'), new NullOutput());
  }

  public function testFindingOneVehicleById()
  {
    $expected = self::$fixture->createVehicles(2, true);
    self::$fixture->persistVehicles($expected);

    $actual = $this->vehicleMapper->findVehicleById(2);
    $this->assertJsonStringEqualsJsonString(json_encode($expected[1]), json_encode($actual));
  }

  public function testGettingAllVehiclesFromDb()
  {
    $expected = self::$fixture->createVehicles(2);
    self::$fixture->persistVehicles($expected);

    $actual = $this->vehicleMapper->getAllVehicles();
    $this->assertCount(2, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testSavingVehicle()
  {
    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "vehicle");
    $this->assertCount(0, $actual);

    $expected = self::$fixture->createVehicles(1);
    $this->vehicleMapper->saveVehicle($expected[0]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "vehicle");
    $this->assertCount(1, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testSavingVehicleReturnsCreatedVehicle()
  {
    [$vehicle] = self::$fixture->createVehicles(1);
    $expected = $this->vehicleMapper->saveVehicle($vehicle);

    [$actual] = MapperCommonMethods::getAllFromDBTable(self::$pdo, "vehicle");
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }

  public function testDeletingVehicle()
  {
    $expected = self::$fixture->createVehicles(2);
    self::$fixture->persistVehicles($expected);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "vehicle");
    $this->assertCount(2, $actual);

    $bool = $this->vehicleMapper->deleteVehicle(1);
    $this->assertTrue($bool);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "vehicle");
    $this->assertCount(1, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($expected[1]), json_encode($actual[0]));
  }

  public function testEditingVehicle()
  {
    [$vehicle, $secondVehicle] = self::$fixture->createVehicles(2);
    [$editedVehicle] = self::$fixture->createVehicles(1, 2);
    self::$fixture->persistVehicles([$vehicle, $secondVehicle]);

    $allVehiclesInDb = MapperCommonMethods::getAllFromDBTable(self::$pdo, "vehicle");
    $this->assertCount(2, $allVehiclesInDb);

    $this->vehicleMapper->updateVehicle($editedVehicle, $vehicle->getId());

    $allVehiclesInDb = MapperCommonMethods::getAllFromDBTable(self::$pdo, "vehicle");
    $this->assertCount(2, $allVehiclesInDb);

    foreach ($allVehiclesInDb as $vehicle) {
      if ($vehicle->getId() === $editedVehicle->getId()) {
        $actual = $vehicle;
      } else {
        $nonEditedVehicle = $vehicle;
      }
    }

    $this->assertJsonStringEqualsJsonString(json_encode($editedVehicle), json_encode($actual));
    $this->assertJsonStringNotEqualsJsonString(json_encode($editedVehicle), json_encode($nonEditedVehicle));
  }

  public function testEditingVehicleReturnsUpdateVehicle()
  {
    [$vehicle] = self::$fixture->createVehicles(1);
    [$editedVehicle] = self::$fixture->createVehicles(1);
    self::$fixture->persistVehicles([$vehicle]);

    $editedVehicle = $this->vehicleMapper->updateVehicle($editedVehicle, $editedVehicle->getId());
    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "vehicle");

    $this->assertJsonStringEqualsJsonString(json_encode($editedVehicle), json_encode($actual[0]));
  }
}
