<?php

use app\models\domains\request\RequestMapper;
use app\models\domains\request_entry\EntryMapper;
use common\MapperCommonMethods;
use fixtures\EntryFixture;
use fixtures\PartFixture;
use fixtures\RequestFixture;
use fixtures\VehicleFixture;
use Phinx\Console\PhinxApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class RequestMapperTest extends TestCase
{
  private static PhinxApplication $phinxApp;
  private static $pdo;
  private static RequestFixture $fixture;
  private static PartFixture $partFixture;
  private static EntryFixture $entryFixture;
  private RequestMapper $requestMapper;
  private EntryMapper $entryMapper;
  private VehicleFixture $vehicleFixture;

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
    $this->entryMapper = new EntryMapper(self::$pdo);
    self::$partFixture = new PartFixture(self::$pdo);
    $this->vehicleFixture = new VehicleFixture(self::$pdo);
    self::$entryFixture = new EntryFixture(self::$pdo, self::$partFixture);
  }

  protected function tearDown(): void
  {
    self::$phinxApp->run(new StringInput('rollback -e testing -t 0'), new NullOutput());
  }

  public function testFindOneRequestById()
  {
    $requests = self::$fixture->createRequests(2, true);
    self::$fixture->persistRequests($requests);
    $entries = self::$entryFixture->createEntriesWithPartsAndPersistToRequest(3, $requests[0], true);
    $requests[0]->addEntries($entries);
    $entries = self::$entryFixture->createEntriesWithPartsAndPersistToRequest(2, $requests[1], true, 3);
    $requests[1]->addEntries($entries);

    $entriesWithoutParts = self::$entryFixture->createEntriesWithoutPartsAndPersistToRequest(1, $requests[0], true, 5);
    $requests[0]->addEntries($entriesWithoutParts);

    $requests[1]->setEntries($entries);
    $expected = json_encode($requests[0]);

    $actual = $this->requestMapper->findOneById($requests[0]->getId());
    $this->assertJsonStringEqualsJsonString($expected, json_encode($actual));
  }

  public function testFindingOneByPhiAndYear()
  {
    $requests = self::$fixture->createRequests(2, true);
    self::$fixture->persistRequests($requests);
    $entries = self::$entryFixture->createEntriesWithPartsAndPersistToRequest(3, $requests[0], true);
    $requests[0]->addEntries($entries);
    $entries = self::$entryFixture->createEntriesWithPartsAndPersistToRequest(2, $requests[1], true, 3);
    $requests[1]->addEntries($entries);

    $entriesWithoutParts = self::$entryFixture->createEntriesWithoutPartsAndPersistToRequest(1, $requests[0], true, 5);
    $requests[0]->addEntries($entriesWithoutParts);

    $requests[1]->setEntries($entries);
    $expected = json_encode($requests[0]);

    $actual = $this->requestMapper->findOneByPhiAndYear($requests[0]->getFirstPhi(), $requests[0]->getYear());
    $this->assertJsonStringEqualsJsonString($expected, json_encode($actual));
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

    $entries = self::$entryFixture->createEntriesWithPartsAndPersistToRequest(3, $requests[1], true);
    $requests[1]->addEntries($entries);
    $entries = self::$entryFixture->createEntriesWithPartsAndPersistToRequest(4, $requests[2], true, 3);
    $requests[2]->addEntries($entries);
    $entries = self::$entryFixture->createEntriesWithPartsAndPersistToRequest(1, $requestsWithSamePhi[0], true, 7);
    $requestsWithSamePhi[0]->addEntries($entries);

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
    $requestsWithDateInInterval = self::$fixture->sortRequestsByDate($requestsWithDateInInterval);

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

    $result = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(6, $result);

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
    [$vehicle] = $this->vehicleFixture->createVehicles(1);
    $this->vehicleFixture->persistVehicles([$vehicle]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(0, $actual);

    $request = self::$fixture->createOneRequestWithVehicle($vehicle->getId());
    $this->requestMapper->saveRequest($request[0]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(1, $actual);

    $this->assertJsonStringEqualsJsonString(json_encode($request[0]), json_encode($actual[0]));
  }

  public function testSavingRequestReturnsCreatedRequest()
  {
    [$vehicle] = $this->vehicleFixture->createVehicles(1);
    $this->vehicleFixture->persistVehicles([$vehicle]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(0, $actual);

    $request = self::$fixture->createOneRequestWithVehicle($vehicle->getId());
    $expected = $this->requestMapper->saveRequest($request[0]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(1, $actual);

    $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual[0]));
  }

  public function testUpdatingRequest()
  {
    [$vehicle, $secondVehicle] = $this->vehicleFixture->createVehicles(2);
    $this->vehicleFixture->persistVehicles([$vehicle, $secondVehicle]);

    [$request] = self::$fixture->createOneRequestWithVehicle($vehicle->getId());
    [$secondRequest] = self::$fixture->createRequests(1, true, 1);
    [$editedRequest] = self::$fixture->createOneRequestWithVehicle($secondVehicle->getId());
    self::$fixture->persistRequests([$request, $secondRequest]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(2, $actual);

    $this->requestMapper->updateRequest($editedRequest, $request->getId());

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(2, $actual);

    $this->assertJsonStringEqualsJsonString(json_encode($actual[0]), json_encode($editedRequest));
    $this->assertJsonStringNotEqualsJsonString(json_encode($actual[1]), json_encode($editedRequest));
  }

  public function testUpdatingRequestReturnsNewRequest()
  {
    [$vehicle, $secondVehicle] = $this->vehicleFixture->createVehicles(2);
    $this->vehicleFixture->persistVehicles([$vehicle, $secondVehicle]);

    [$request] = self::$fixture->createOneRequestWithVehicle($vehicle->getId());
    [$secondRequest] = self::$fixture->createRequests(1, true, 1);
    [$editedRequest] = self::$fixture->createOneRequestWithVehicle($secondVehicle->getId());
    self::$fixture->persistRequests([$request, $secondRequest]);

    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    $this->assertCount(2, $actual);

    $requestReturned = $this->requestMapper->updateRequest($editedRequest, $request->getId());
    $this->assertJsonStringEqualsJsonString(json_encode($editedRequest), json_encode($requestReturned));
  }

  public function testUpdatingRequestChangesChildrenToo()
  {
    [$request] = self::$fixture->createRequests(1, true);
    [$editedRequest] = self::$fixture->createRequests(1, true);
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

    $this->requestMapper->updateRequest($editedRequest, $request->getId());

    $childrenOfEditedRequestEntries = $this->entryMapper->findAllByPhiAndYear(
      $editedRequest->getFirstPhi(),
      $editedRequest->getYear()
    );
    $this->assertCount(2, $childrenOfEditedRequestEntries);
    $actual = MapperCommonMethods::getAllFromDBTable(self::$pdo, "request");
    //set the entries to empty array as to compare only the requests
    $actual[0]->setEntries([]);
    $this->assertCount(1, $actual);
    $this->assertJsonStringEqualsJsonString(json_encode($actual[0]), json_encode($editedRequest));
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

  public function testFindingByVehicle()
  {
    [$vehicle] = $this->vehicleFixture->createVehicles(1);
    $this->vehicleFixture->persistVehicles([$vehicle]);

    [$requestBelonginToVehicleOne] = self::$fixture->createOneRequestWithVehicle($vehicle->getId());
    [$secondRequestBelonginToVehicleOne] = self::$fixture->createOneRequestWithVehicle($vehicle->getId(), 1);
    [$requestWithoutVehicle] = self::$fixture->createOneRequestWithVehicle(null, 2);
    self::$fixture->persistRequests([$requestBelonginToVehicleOne, $secondRequestBelonginToVehicleOne, $requestWithoutVehicle]);
    $entries = self::$entryFixture->createEntriesWithPartsAndPersistToRequest(3, $requestBelonginToVehicleOne, true);
    $requestBelonginToVehicleOne->addEntries($entries);
    $entries = self::$entryFixture->createEntriesWithPartsAndPersistToRequest(rand(1, 3), $secondRequestBelonginToVehicleOne, true, 3);
    $secondRequestBelonginToVehicleOne->addEntries($entries);

    $actual = $this->requestMapper->findAllByVehicle($vehicle->getId());

    $this->assertCount(2, $actual);
    $this->assertJsonStringEqualsJsonString(
      json_encode([$requestBelonginToVehicleOne, $secondRequestBelonginToVehicleOne]),
      json_encode($actual)
    );
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

    $count = 0;
    foreach ($allRequsts as $request) {
      $amountOfEntriesToCreate = rand(1, 3);
      $entries = self::$entryFixture->createEntriesWithPartsAndPersistToRequest($amountOfEntriesToCreate, $request, true, $count);
      $request->addEntries($entries);
      $count += $amountOfEntriesToCreate;
    }

    $requestsWithDateInInterval = self::$fixture->sortRequestsByDate($requestsWithDateInInterval);
    return $requestsWithDateInInterval;
  }
}
