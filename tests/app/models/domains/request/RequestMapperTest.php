<?php

use app\models\domains\request\RequestEntity;
use app\models\domains\request\RequestFactory;
use app\models\domains\request\RequestMapper;
use fixtures\RequestFixture;
use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class RequestMapperTest extends TestCase
{
  private static PhinxApplication $phinxApp;
  private static $pdo;
  private static RequestFixture $fixture;
  private RequestMapper $requestMapper;

  public static function setUpBeforeClass(): void
  {
    self::$pdo = include(TEST_DIR . "/setDatabaseForTestsScript.php");
    self::$phinxApp = new PhinxApplication();
    self::$fixture = new RequestFixture(self::$pdo);
  }

  public static function tearDownAfterClass(): void
  {
    self::$pdo = null;
  }

  protected function setUp(): void
  {
    self::$phinxApp->setAutoExit(false);
    self::$phinxApp->run(new StringInput('migrate -e testing'), new NullOutput());
    $this->requestMapper = new RequestMapper(self::$pdo);
  }

  protected function tearDown(): void
  {
    self::$phinxApp->run(new StringInput('rollback -e testing -t 0'), new NullOutput());
  }

  public function testFindingOneByPhiAndYear()
  {
    $expected = self::$fixture->getFindingOneByPhiAndYearFixture();
    $actual = $this->requestMapper->findOneByPhiAndYear($expected->getFirstPhi(), $expected->getSecondPhi(), $expected->getYear());
    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
  }
  public function testFindingManyByFullPhiReturnsCorrectNumberOfRecordsAndIsOrderedByYear()
  {
    $expected = self::$fixture->getFindingByFullPhiFixture();
    $result = $this->requestMapper->findManyByFullPhi($expected[0]->getFirstPhi(), $expected[0]->getSecondPhi());
    $this->assertJsonStringEqualsJsonString(json_encode($result), json_encode($expected));
  }

  public function testSaveManyRequests()
  {
    $arrayOfRequestObjects = [$this->createMock(RequestEntity::class), $this->createMock(RequestEntity::class), $this->createMock(RequestEntity::class)];

    foreach ($arrayOfRequestObjects as $key => $request) {
      $requestMapperMock = $this->getMockBuilder(RequestMapper::class)
        ->disableOriginalConstructor()
        ->onlyMethods(["saveRequest"])
        ->getMock();
    }
    $requestMapperMock
      ->expects($this->exactly(count($arrayOfRequestObjects)))
      ->method("saveRequest")
      ->with($request);
    $requestMapperMock->saveManyRecords($arrayOfRequestObjects);
  }

  public function testFindingByYearMonthAndDayInterval()
  {
    $startDate = [2002, 2, 20];
    $endDate = [2003, 4, 22];
    [
      'requests' => $requests,
    ] = self::$fixture->getFixturesForIntervalTestingByDate($startDate, $endDate);

    $actual = $this->requestMapper->findAllByDateInterval($startDate, $endDate);
    $this->assertJsonStringEqualsJsonString(json_encode($requests), json_encode($actual));
  }

  public function testFindingByYearAndMonthInterval()
  {
    $startDate = [2002, 2];
    $endDate = [2003, 4];
    [
      'requests' => $requests
    ] = self::$fixture->getFixturesForIntervalTestingByDate($startDate, $endDate);

    $actual = $this->requestMapper->findAllByDateInterval($startDate, $endDate);
    $this->assertJsonStringEqualsJsonString(json_encode($requests), json_encode($actual));
  }

  public function testFindingByYearInterval()
  {
    $startDate = [2002];
    $endDate = [2003];
    [
      'requests' => $requests
    ] = self::$fixture->getFixturesForIntervalTestingByDate($startDate, $endDate);

    $actual = $this->requestMapper->findAllByDateInterval($startDate, $endDate);
    $this->assertJsonStringEqualsJsonString(json_encode($requests), json_encode($actual));
  }

  public function testFindingByStartingYearMonthAndDayOnly()
  {
    $startDate = [2002, 2, 20];
    [
      'requests' => $requests
    ] = self::$fixture->getFixturesForIntervalTestingByDate($startDate);

    $actual = $this->requestMapper->findAllByDateInterval($startDate);
    $this->assertJsonStringEqualsJsonString(json_encode($requests), json_encode($actual));
  }

  public function testFindingByStartingYearAndMonthOnly()
  {
    $startDate = [2002, 2];
    [
      'requests' => $requests
    ] = self::$fixture->getFixturesForIntervalTestingByDate($startDate);

    $actual = $this->requestMapper->findAllByDateInterval($startDate);
    $this->assertJsonStringEqualsJsonString(json_encode($requests), json_encode($actual));
  }

  public function testFindingByStartingYearOnly()
  {
    $startDate = [2002];
    [
      'requests' => $requests
    ] = self::$fixture->getFixturesForIntervalTestingByDate($startDate);

    $actual = $this->requestMapper->findAllByDateInterval($startDate);
    $this->assertJsonStringEqualsJsonString(json_encode($requests), json_encode($actual));
  }

  public function testSavingOneUnique()
  {
    $request = self::$fixture->getSavingOneUniqueFixture();
    $this->requestMapper->saveRequest($request);
    $dbRecord = self::$pdo->query("SELECT * FROM request WHERE phi_first_part=15 AND phi_second_part=2000 AND year=2021")->fetchAll();
    $this->assertCount(1, $dbRecord);
    $dbRecord = RequestFactory::createRequestEntityFromRecord($dbRecord[0]);
    $this->assertJsonStringEqualsJsonString(json_encode($dbRecord), json_encode($request));
  }

  public function testUpdatingRequest()
  {
    $fixture = self::$fixture->getUpdatingRequestFixture();
    $this->requestMapper->saveRequest($fixture[0]);
    $requestWithDifferentDay = $fixture[1];
    $this->requestMapper->updateRequest($fixture[1]);
    $dbRecord = self::$pdo->query("SELECT * FROM request WHERE phi_first_part=15 AND phi_second_part=2000 AND year=2021")->fetchAll();
    $this->assertCount(1, $dbRecord);
    $dbRecord = RequestFactory::createRequestEntityFromRecord($dbRecord[0]);
    $this->assertJsonStringEqualsJsonString(json_encode($dbRecord), json_encode($requestWithDifferentDay));
  }
}
