<?php

use app\models\domains\vehicle\VehicleEntity;
use app\models\domains\vehicle\VehicleFactory;
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
    $bool = $this->vehicleMapper->saveVehicle($expected[0]);
    $this->assertTrue($bool);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "vehicle");
    $this->assertCount(1, $actual);
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
    [$editedVehicle] = self::$fixture->createVehicles(1);
    self::$fixture->persistVehicles([$vehicle, $secondVehicle]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "vehicle");
    $this->assertCount(2, $actual);

    $bool = $this->vehicleMapper->updateVehicle($editedVehicle, $editedVehicle->getId());
    $this->assertTrue($bool);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "vehicle");
    $this->assertCount(2, $actual);

    $this->assertJsonStringEqualsJsonString(json_encode($editedVehicle), json_encode($actual[0]));
    MapperCommonMethods::testTwoEntitiesAreNotEqualWithoutCheckingForId($secondVehicle, $editedVehicle);
  }
}
