<?php

use app\models\domains\request\RequestEntity;
use app\models\domains\request\RequestFactory;
use app\models\domains\request\RequestMapper;
use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class RequestMapperTest extends TestCase
{
  private static PhinxApplication $phinxApp;
  private static $pdo;
  private RequestMapper $requestMapper;

  private int  $firstPartOfPhi = 15;
  private int  $secondPartOfPhi = 2000;
  private int  $year = 2021;
  private int  $month = 05;
  private int  $day = 15;
  private int  $differentYearFortTest = 2020;
  private int  $differentFirstPartOfPhi = 16;
  private int $yearOutsideOfInterval = 2022;
  private int $differentValueOfDay = 115;
  private RequestEntity  $request;
  private RequestEntity $requestWithSamePhi;
  private RequestEntity $requestWithDifferentPhi;
  private RequestEntity $requestWithDifferentYear;
  private RequestEntity $requestWithYearOutsideOfInterval;
  private RequestEntity $requestWithDIfferentDay;

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
    $this->requestMapper = new RequestMapper(self::$pdo);

    $this->request = new RequestEntity(
      $this->firstPartOfPhi,
      $this->secondPartOfPhi,
      $this->year,
      $this->month,
      $this->day
    );

    $this->requestWithSamePhi = new RequestEntity(
      $this->firstPartOfPhi,
      $this->secondPartOfPhi,
      $this->differentYearFortTest,
      $this->month,
      $this->day
    );
    $this->requestWithDifferentPhi = new RequestEntity(
      $this->differentFirstPartOfPhi,
      $this->secondPartOfPhi,
      $this->year,
      $this->month,
      $this->day
    );

    $this->requestWithDifferentYear = new RequestEntity(
      $this->firstPartOfPhi,
      $this->secondPartOfPhi,
      $this->differentYearFortTest,
      $this->month,
      $this->day
    );

    $this->requestWithYearOutsideOfInterval = new RequestEntity(
      $this->firstPartOfPhi,
      $this->secondPartOfPhi,
      $this->yearOutsideOfInterval,
      $this->month,
      $this->day
    );

    $this->requestWithDIfferentDay = new RequestEntity(
      $this->firstPartOfPhi,
      $this->secondPartOfPhi,
      $this->year,
      $this->month,
      $this->differentValueOfDay
    );
  }

  protected function tearDown(): void
  {
    self::$phinxApp->run(new StringInput('rollback -e testing -t 0'), new NullOutput());
  }

  public function testFindingOneByPhiAndYear()
  {
    $this->requestMapper->saveRequest($this->request);
    $result = $this->requestMapper->findOneByPhiAndYear($this->firstPartOfPhi, $this->secondPartOfPhi, $this->year);
    $this->assertJsonStringEqualsJsonString(json_encode($this->request), json_encode($result));
  }

  public function testFindingManyByFullPhiReturnsCorrectNumberOfRecordsAndIsOrderedByYear()
  {
    $this->requestMapper->saveManyRecords([$this->request, $this->requestWithSamePhi, $this->requestWithDifferentPhi]);
    $result = $this->requestMapper->findManyByFullPhi($this->firstPartOfPhi, $this->secondPartOfPhi);
    $this->assertJsonStringEqualsJsonString(json_encode($result), json_encode([$this->requestWithSamePhi, $this->request]));
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

  public function testFindingManyByYearInterval()
  {
    $this->requestMapper->saveManyRecords([$this->request, $this->requestWithDifferentYear, $this->requestWithYearOutsideOfInterval]);
    $result = $this->requestMapper->findAllByDateInterval($this->differentYearFortTest, $this->year);

    $this->assertJsonStringEqualsJsonString(json_encode($result), json_encode([$this->requestWithDifferentYear, $this->request]));
  }

  public function testFindingManyByYearWorksIfOnlyStartDateIsGiven()
  {
    $this->requestMapper->saveManyRecords([$this->request, $this->requestWithDifferentYear, $this->requestWithYearOutsideOfInterval]);
    $result = $this->requestMapper->findAllByDateInterval($this->year);

    $this->assertJsonStringEqualsJsonString(json_encode($result), json_encode([$this->request]));
  }

  public function testSavingOneUnique()
  {
    $result = $this->requestMapper->saveRequest($this->request);
    $this->assertTrue($result);
    $dbRecord = self::$pdo->query("SELECT * FROM request WHERE phi_first_part=15 AND phi_second_part=2000 AND year=2021")->fetchAll();
    $this->assertCount(1, $dbRecord);
    $dbRecord = RequestFactory::createRequestEntityFromRecord($dbRecord[0]);
    $this->assertJsonStringEqualsJsonString(json_encode($dbRecord), json_encode($this->request));
  }

  public function testSavingDuplicateUpdates()
  {
    $result = $this->requestMapper->saveRequest($this->request);
    $result = $this->requestMapper->saveRequest($this->requestWithDIfferentDay);
    $this->assertTrue($result);
    $dbRecord = self::$pdo->query("SELECT * FROM request WHERE phi_first_part=15 AND phi_second_part=2000 AND year=2021")->fetchAll();
    $this->assertCount(1, $dbRecord);
    $dbRecord = RequestFactory::createRequestEntityFromRecord($dbRecord[0]);
    $this->assertJsonStringEqualsJsonString(json_encode($dbRecord), json_encode($this->requestWithDIfferentDay));
  }
}
