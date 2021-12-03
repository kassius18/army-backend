<?php

use app\models\domains\request\RequestEntity;
use app\models\domains\request\RequestMapper;
use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class RequestMapperTest extends TestCase
{
  /* TODO: 
   * You can use sql to save but not to check if the find methods work
   * <02-12-21, yourname> */

  private $phinxApp;
  private $requestMapper;
  private $pdo;

  protected function setUp(): void
  {
    $this->pdo = include(TEST_DIR . "/setDatabaseForTestsScript.php");
    $this->phinxApp = new PhinxApplication();
    $this->phinxApp->setAutoExit(false);
    $this->phinxApp->run(new StringInput('migrate -e testing'), new NullOutput());
    $this->requestMapper = new RequestMapper($this->pdo);
  }

  protected function tearDown(): void
  {
    $this->phinxApp->run(new StringInput('rollback -e testing -t 0'), new NullOutput());
  }

  public function testFindingOneByPhiAndYear()
  {
    $firstPartOfPhi = 15;
    $secondPartOfPhi = 2000;
    $year = 2021;
    $month = 05;
    $day = 15;
    $request = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $year, $month, $day);
    $this->requestMapper->saveRequest($request);
    $result = $this->requestMapper->findOneByPhiAndYear($firstPartOfPhi, $secondPartOfPhi, $year);
    $this->assertSame($request->getDay(), $result[0]['day']);
    $this->assertSame($request->getMonth(), $result[0]['month']);
    $this->assertSame($request->getYear(), $result[0]['year']);
    $this->assertSame($request->getFirstPhi(), $result[0]['phi_first_part']);
    $this->assertSame($request->getSecondPhi(), $result[0]['phi_second_part']);
  }

  public function testFindingManyByFullPhiReturnsCorrectNumberOfRecordsAndIsOrderedByYear()
  {
    $firstPartOfPhi = 15;
    $secondPartOfPhi = 2000;
    $year = 2021;
    $month = 05;
    $day = 15;
    $differentYearFortTest = 2020;
    $differentFirstPartOfPhi = 16;
    $request = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $year, $month, $day);
    $requestWithSamePhi = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $differentYearFortTest, $month, $day);
    $requestWithDifferentPhi = new RequestEntity($differentFirstPartOfPhi, $secondPartOfPhi, $year, $month, $day);
    $this->requestMapper->saveManyRecords([$request, $requestWithSamePhi, $requestWithDifferentPhi]);
    $result = $this->requestMapper->findManyByFullPhi($firstPartOfPhi, $secondPartOfPhi);
    $this->assertJsonStringEqualsJsonString(json_encode($result), json_encode([$requestWithSamePhi, $request]));
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
    $firstPartOfPhi = 15;
    $secondPartOfPhi = 2000;
    $year = 2021;
    $month = 05;
    $day = 15;
    $differentYearFortTest = 2020;
    $yearOutsideOfInterval = 2022;
    $request = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $year, $month, $day);
    $requestWithDifferentYear = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $differentYearFortTest, $month, $day);
    $requestWithYearOutsideOfInterval = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $yearOutsideOfInterval, $month, $day);
    $this->requestMapper->saveManyRecords([$request, $requestWithDifferentYear, $requestWithYearOutsideOfInterval]);
    $result = $this->requestMapper->findAllByDateInterval($differentYearFortTest, $year);

    $this->assertJsonStringEqualsJsonString(json_encode($result), json_encode([$requestWithDifferentYear, $request]));
  }

  public function testFindingManyByYearWorksIfOnlyStartDateIsGiven()
  {
    $firstPartOfPhi = 15;
    $secondPartOfPhi = 2000;
    $year = 2021;
    $month = 05;
    $day = 15;
    $differentYearFortTest = 2020;
    $yearOutsideOfInterval = 2022;
    $request = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $year, $month, $day);
    $requestWithDifferentYear = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $differentYearFortTest, $month, $day);
    $requestWithYearOutsideOfInterval = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $yearOutsideOfInterval, $month, $day);
    $this->requestMapper->saveManyRecords([$request, $requestWithDifferentYear, $requestWithYearOutsideOfInterval]);
    $result = $this->requestMapper->findAllByDateInterval($year);

    $this->assertJsonStringEqualsJsonString(json_encode($result), json_encode([$request]));
  }

  public function testSavingOneUnique()
  {
    $firstPartOfPhi = 15;
    $secondPartOfPhi = 2000;
    $year = 2021;
    $month = 05;
    $day = 15;
    $request = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $year, $month, $day);

    $result = $this->requestMapper->saveRequest($request);
    $this->assertTrue($result);

    $dbRecord = $this->pdo->query("SELECT * FROM request WHERE phi_first_part=15 AND phi_second_part=2000 AND year=2021")->fetchAll();
    $this->assertSame($request->getDay(), $dbRecord[0]['day']);
    $this->assertSame($request->getMonth(), $dbRecord[0]['month']);
    $this->assertSame($request->getYear(), $dbRecord[0]['year']);
    $this->assertSame($request->getFirstPhi(), $dbRecord[0]['phi_first_part']);
    $this->assertSame($request->getSecondPhi(), $dbRecord[0]['phi_second_part']);
  }

  public function testSavingDuplicateUpdates()
  {
    $firstPartOfPhi = 15;
    $secondPartOfPhi = 2000;
    $year = 2021;
    $month = 05;
    $day = 15;
    $differentValueOfDay = 115;
    $request = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $year, $month, $day);
    $result = $this->requestMapper->saveRequest($request);
    $updatedRequest = new RequestEntity($firstPartOfPhi, $secondPartOfPhi, $year, $month, $differentValueOfDay);
    $result = $this->requestMapper->saveRequest($updatedRequest);
    $this->assertTrue($result);
    $dbRecord = $this->pdo->query("SELECT * FROM request WHERE phi_first_part=15 AND phi_second_part=2000 AND year=2021")->fetchAll();
    $this->assertSame($updatedRequest->getDay(), $dbRecord[0]['day']);
    $this->assertSame($updatedRequest->getMonth(), $dbRecord[0]['month']);
    $this->assertSame($updatedRequest->getYear(), $dbRecord[0]['year']);
    $this->assertSame($updatedRequest->getFirstPhi(), $dbRecord[0]['phi_first_part']);
    $this->assertSame($updatedRequest->getSecondPhi(), $dbRecord[0]['phi_second_part']);
    $this->assertCount(1, $dbRecord);
  }
}
