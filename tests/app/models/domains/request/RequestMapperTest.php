<?php

use app\models\domains\request\RequestMapper;
use app\models\domains\request_entry\EntryMapper;
use common\MapperCommonMethods;
use fixtures\EntryFixture;
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
  private static EntryFixture $entryFixture;
  private RequestMapper $requestMapper;
  private EntryMapper $entryMapper;

  public static function setUpBeforeClass(): void
  {
    self::$pdo = include(TEST_DIR . "/setDatabaseForTestsScript.php");
    self::$phinxApp = new PhinxApplication();
    self::$fixture = new RequestFixture(self::$pdo);
    self::$entryFixture = new EntryFixture(self::$pdo);
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
    $this->entryMapper = new EntryMapper(self::$pdo);
  }

  protected function tearDown(): void
  {
    self::$phinxApp->run(new StringInput('rollback -e testing -t 0'), new NullOutput());
  }

  public function testFindOneEntryById()
  {
    $expected = self::$fixture->createRequests(2, true);
    self::$fixture->persistRequests($expected);
    $actual = $this->requestMapper->findOneById($expected[0]->getId());
    $this->assertJsonStringEqualsJsonString(json_encode($expected[0]), json_encode($actual));
  }

  public function testFindingOneByPhiAndYear()
  {
    $expected = self::$fixture->createRequests(2, true);
    self::$fixture->persistRequests($expected);
    $actual = $this->requestMapper->findOneByPhiAndYear(
      $expected[0]->getFirstPhi(),
      $expected[0]->getYear()
    );
    $this->assertJsonStringEqualsJsonString(json_encode($expected[0]), json_encode($actual));
  }

  public function testFindingManyByPhiSortedByYear()
  {
    $requests = self::$fixture->createRequests(3, true);
    $firstPartOfPhi = rand();
    $requestsWithSamePhi = self::$fixture->createRequestsWithInputs(
      2,
      ["firstPartOfPhi" => $firstPartOfPhi],
      true,
      3
    );
    self::$fixture->persistRequests([...$requests, ...$requestsWithSamePhi]);
    $requestsWithSamePhi = self::$fixture->sortRequestsByYear($requestsWithSamePhi);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(5, $actual);

    $actual = $this->requestMapper->findManyByPhi($firstPartOfPhi);
    $this->assertCount(2, $actual);

    $this->assertJsonStringEqualsJsonString(
      json_encode($requestsWithSamePhi),
      json_encode($actual)
    );
  }


  public function testFindingByYearMonthAndDayInterval()
  {
    $startDate = [2003, 4, 21];
    $endDate = [2003, 4, 22];

    $requestsWithDateInInterval = $this->getRequestsFromDateIntervals($startDate, $endDate);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(10, $actual);

    $actual = $this->requestMapper->findAllByDateInterval($startDate, $endDate);
    $this->assertCount(4, $actual);
    $this->assertJsonStringEqualsJsonString(
      json_encode($requestsWithDateInInterval),
      json_encode($actual)
    );
  }

  public function testFindingByYearMonthAndDayIntervalWithOnlyStartDate()
  {
    $startDate = [2003, 4, 21];

    $requestsWithDateInInterval = $this->getRequestsFromDateIntervals($startDate);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(10, $actual);

    $actual = $this->requestMapper->findAllByDateInterval($startDate);
    $this->assertCount(4, $actual);
    $this->assertJsonStringEqualsJsonString(
      json_encode($requestsWithDateInInterval),
      json_encode($actual)
    );
  }

  public function testFindingByYearAndMonthInterval()
  {
    $startDate = [2002, 2];
    $endDate = [2003, 4];
    $requestsWithDateInInterval = $this->getRequestsFromDateIntervals($startDate, $endDate);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(6, $actual);

    $actual = $this->requestMapper->findAllByDateInterval($startDate, $endDate);
    $this->assertCount(3, $actual);
    $this->assertJsonStringEqualsJsonString(
      json_encode($requestsWithDateInInterval),
      json_encode($actual)
    );
  }

  public function testFindingByYearAndMonthIntervalWithOnlyStartDate()
  {
    $startDate = [2002, 2];
    $requestsWithDateInInterval = $this->getRequestsFromDateIntervals($startDate);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(6, $actual);

    $actual = $this->requestMapper->findAllByDateInterval($startDate);
    $this->assertCount(3, $actual);
    $this->assertJsonStringEqualsJsonString(
      json_encode($requestsWithDateInInterval),
      json_encode($actual)
    );
  }

  public function testFindingByYearInterval()
  {
    $startDate = [2002];
    $endDate = [2003];
    $requestsWithDateInInterval = $this->getRequestsFromDateIntervals($startDate, $endDate);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(3, $actual);

    $actual = $this->requestMapper->findAllByDateInterval($startDate, $endDate);
    $this->assertCount(2, $actual);
    $this->assertJsonStringEqualsJsonString(
      json_encode($requestsWithDateInInterval),
      json_encode($actual)
    );
  }

  public function testFindingByYearWithOnlyStartDate()
  {
    $startDate = [2002];
    $requestsWithDateInInterval = $this->getRequestsFromDateIntervals($startDate);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(3, $actual);

    $actual = $this->requestMapper->findAllByDateInterval($startDate);
    $this->assertCount(2, $actual);
    $this->assertJsonStringEqualsJsonString(
      json_encode($requestsWithDateInInterval),
      json_encode($actual)
    );
  }

  public function testSavingRequest()
  {
    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(0, $actual);

    $request = self::$fixture->createRequests(1, true);
    $bool = $this->requestMapper->saveRequest($request[0]);
    $this->assertTrue($bool);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(1, $actual);

    $this->assertJsonStringEqualsJsonString(json_encode($request[0]), json_encode($actual[0]));
  }

  public function testUpdatingRequest()
  {
    [$request, $secondRequest, $editedRequest] = self::$fixture->createRequests(3, true);
    self::$fixture->persistRequests([$request, $secondRequest]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(2, $actual);

    $bool = $this->requestMapper->updateRequest($editedRequest, $request->getId());
    $this->assertTrue($bool);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(2, $actual);
    MapperCommonMethods::testTwoEntitiesAreEqualWithoutCheckingForId($actual[0], $editedRequest);
    MapperCommonMethods::testTwoEntitiesAreNotEqualWithoutCheckingForId($actual[1], $editedRequest);
  }

  public function testUpdatingRequestChangesChildrenToo()
  {
    [$request, $editedRequest] = self::$fixture->createRequests(2, true);
    self::$fixture->persistRequests([$request]);
    $entries = self::$entryFixture->createEntries(2);
    self::$entryFixture->persistEntries(
      $entries,
      ["firstPartOfPhi" => $request->getFirstPhi(), "year" => $request->getYear()]
    );

    $actualRequests = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $actualEntries = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertCount(1, $actualRequests);
    $this->assertCount(2, $actualEntries);

    $bool = $this->requestMapper->updateRequest($editedRequest, $request->getId());
    $this->assertTrue($bool);

    $childrenOfEditedRequestEntries = $this->entryMapper->findAllByPhiAndYear(
      $editedRequest->getFirstPhi(),
      $editedRequest->getYear()
    );
    $this->assertCount(2, $childrenOfEditedRequestEntries);
    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(1, $actual);
    MapperCommonMethods::testTwoEntitiesAreEqualWithoutCheckingForId($actual[0], $editedRequest);
  }

  public function testDeletingOneById()
  {
    $expected = self::$fixture->createRequests(2, true);
    self::$fixture->persistRequests($expected);

    $bool = $this->requestMapper->deleteRequestById($expected[0]->getId());
    $this->assertTrue($bool);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(1, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($expected[1]), json_encode($actual[0]));
  }

  public function testDeletingRequestDeletesChildrenEntriesToo()
  {
    $expected = self::$fixture->createRequests(2, true);
    self::$fixture->persistRequests($expected);
    $entriesForRequestOne = self::$entryFixture->createEntries(3, true);
    self::$entryFixture->persistEntries(
      $entriesForRequestOne,
      ["firstPartOfPhi" => $expected[0]->getFirstPhi(), "year" => $expected[0]->getYear()]
    );
    $entriesForRequestTwo = self::$entryFixture->createEntries(2, true, 4);
    self::$entryFixture->persistEntries(
      $entriesForRequestTwo,
      ["firstPartOfPhi" => $expected[1]->getFirstPhi(), "year" => $expected[1]->getYear()]
    );

    $actualRequests = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(2, $actualRequests);

    $actualEntries = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertCount(5, $actualEntries);

    $bool = $this->requestMapper->deleteRequestById($expected[0]->getId());
    $this->assertTrue($bool);

    $actualRequests = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertcount(1, $actualRequests);

    $actualEntries = MapperCommonMethods::getAllFromDBTable(self::$pdo, "entry");
    $this->assertcount(2, $actualEntries);
  }

  private function getRequestsFromDateIntervals($startDate, $endDate = null)
  {
    $endDate = $startDate ?: $startDate;
    $requestsWithYearInInterval = self::$fixture->createRequestsWithInputs(
      2,
      [
        "year" => [$startDate[0], $endDate[0]]
      ],
      true
    );
    if (isset($startDate[1])) {
      $requestsWithYearMonthInInterval = self::$fixture->createRequestsWithInputs(
        3,
        [
          "year" => [$startDate[0], $endDate[0]],
          "month" => [$startDate[1], $endDate[1]],
        ],
        true,
        2
      );
    }
    if (isset($startDate[2])) {
      $requestsWithYearMonthDayInInterval = self::$fixture->createRequestsWithInputs(
        4,
        [
          "year" => [$startDate[0], $endDate[0]],
          "month" => [$startDate[1], $endDate[1]],
          "day" => [$startDate[2], $endDate[2]]
        ],
        true,
        5
      );
    }
    $requestsOutsideOfInterval = self::$fixture->createRequests(1, true, 9);

    $allRequsts = [];

    if (isset($requestsWithYearMonthDayInInterval)) {
      $requestsWithDateInInterval = $requestsWithYearMonthDayInInterval;
      $allRequsts = [
        ...$requestsWithYearInInterval,
        ...$requestsWithYearMonthInInterval,
        ...$requestsWithDateInInterval,
        ...$requestsOutsideOfInterval,
      ];
    } else if (isset($requestsWithYearMonthInInterval)) {
      $requestsWithDateInInterval = $requestsWithYearMonthInInterval;
      $allRequsts = [
        ...$requestsWithYearInInterval,
        ...$requestsWithDateInInterval,
        ...$requestsOutsideOfInterval,
      ];
    } else {
      $requestsWithDateInInterval = $requestsWithYearInInterval;
      $allRequsts = [
        ...$requestsWithDateInInterval,
        ...$requestsOutsideOfInterval,
      ];
    }

    self::$fixture->persistRequests($allRequsts);
    $requestsWithDateInInterval = self::$fixture->sortRequestsByDate($requestsWithDateInInterval);
    return $requestsWithDateInInterval;
  }
}
