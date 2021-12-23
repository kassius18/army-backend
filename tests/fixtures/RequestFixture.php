<?php

namespace fixtures;

use app\models\domains\request\RequestEntity;
use app\models\domains\request_entry\EntryEntity;

class RequestFixture
{
  private static int $firstPartOfPhi = 15;
  private static int $secondPartOfPhi = 2000;
  private static int $year = 2021;
  private static int $month = 05;
  private static int $day = 15;
  private static int $differentYear = 2020;
  private static int $differentFirstPartOfPhi = 16;
  private static int $differentDay = 14;
  private static \PDO $pdo;
  private RequestEntity $commonRequest;

  public function __construct(\PDO $pdo)
  {
    self::$pdo = $pdo;

    $entry = $this->createEntryThatBelongsToRequestWithIncrementalId(
      self::$firstPartOfPhi,
      self::$secondPartOfPhi,
      self::$year,
      0
    );

    $this->commonRequest = new RequestEntity(
      self::$firstPartOfPhi,
      self::$secondPartOfPhi,
      self::$year,
      self::$month,
      self::$day,
      [$entry]
    );
  }

  public function getFindingByFullPhiFixture(): array
  {
    $entry = $this->createEntryThatBelongsToRequestWithIncrementalId(
      self::$firstPartOfPhi,
      self::$secondPartOfPhi,
      self::$differentYear,
      1
    );

    $requestWithSamePhi = new RequestEntity(
      self::$firstPartOfPhi,
      self::$secondPartOfPhi,
      self::$differentYear,
      self::$month,
      self::$day,
      [$entry]
    );

    $entryWithDifferentPhi = $this->createEntryThatBelongsToRequestWithIncrementalId(
      self::$differentFirstPartOfPhi,
      self::$secondPartOfPhi,
      self::$differentYear,
      2
    );

    $requestWithDifferentePhi = new RequestEntity(
      self::$differentFirstPartOfPhi,
      self::$secondPartOfPhi,
      self::$differentYear,
      self::$month,
      self::$day,
      [$entryWithDifferentPhi]
    );

    $this->persistDataSet([$this->commonRequest, $requestWithSamePhi, $requestWithDifferentePhi]);
    return [$requestWithSamePhi, $this->commonRequest];
  }

  public function getFindingOneByPhiAndYearFixture(): RequestEntity
  {
    $this->persistDataSet([$this->commonRequest]);
    return $this->commonRequest;
  }

  public function getSavingOneUniqueFixture(): RequestEntity
  {
    return $this->commonRequest;
  }

  public function getUpdatingRequestFixture(): array
  {
    $entry = $this->createEntryThatBelongsToRequestWithIncrementalId(
      self::$firstPartOfPhi,
      self::$secondPartOfPhi,
      self::$year,
      0
    );
    $requestWithDifferentDay = new RequestEntity(
      self::$firstPartOfPhi,
      self::$secondPartOfPhi,
      self::$year,
      self::$month,
      self::$differentDay,
      [$entry]
    );
    return [$this->commonRequest, $requestWithDifferentDay];
  }

  public function getFixturesForIntervalTestingByDate(array $startDate, mixed $endDate = null)
  {
    return $this->createRequestsForIntervalTestAndFilterByDate($startDate, $endDate);
  }

  public function getFindingByYearAndMonthIntervalFixture()
  {
    $startDate = [2002, 2];
    $endDate = [2003, 4];
    return $this->createRequestsForIntervalTestAndFilterByDate($startDate, $endDate);
  }

  public function getFindingByYearIntervalFixture()
  {
    $startDate = [2002];
    $endDate = [2003];
    return $this->createRequestsForIntervalTestAndFilterByDate($startDate, $endDate);
  }

  private function persistDataSet(array $arrayOfRequestObjects): void
  {
    foreach ($arrayOfRequestObjects as $key => $requestEntity) {
      $sql = "INSERT INTO request(
`phi_first_part`,
`phi_second_part`,
`YEAR`,
`MONTH`,
`DAY`
)VALUES";
      $sql .= "(
{$requestEntity->getFirstPhi()},
{$requestEntity->getSecondPhi()},
{$requestEntity->getYear()},
{$requestEntity->getMonth()},
{$requestEntity->getDay()}
)";
      $sqlToInsertEntriesToTestRequest = "INSERT INTO `request_row`(
`request_phi_first_part`,
`request_phi_second_part`,
`request_year`,
`name_number`,
`name`,
`main_part`,
`amount_of_order`,
`unit_of_order`,
`reason_of_order`,
`priority_of_order`,
`observations`
)
VALUES(
    {$requestEntity->getFirstPhi()},
    {$requestEntity->getSecondPhi()},
    {$requestEntity->getYear()},
    'nameNumberTest',
    'nameTest',
    'mainPartTest',
    100,
    'tem',
    99,
    1,
    'obs'
)";
      self::$pdo->query($sql);
      self::$pdo->query($sqlToInsertEntriesToTestRequest);
    }
  }

  private function createRequestsForIntervalTestAndFilterByDate(array $startDate, mixed $endDate = null)
  {
    [$arrayOfRequests, $arrayOfRequestsWithDateKeys] = $this->createRequestsForFindingByInterval();
    $allArraysInsideInterval = $this->filterAllRequestsByDateInterval($arrayOfRequestsWithDateKeys, $startDate, $endDate);

    $this->persistDataSet($arrayOfRequests);
    return $allArraysInsideInterval;
  }

  private function createRequestsForFindingByInterval(): array
  {
    $arrayOfRequestsWithDateKeys = [];
    $arrayOfRequests = [];
    $firstPartOfPhi = 0;
    $incrementalId = 0;
    for ($year = 2000; $year <= 2003; $year++) {
      for ($month = 1; $month <= 4; $month++) {
        for ($day = 20; $day <= 23; $day++) {
          $firstPartOfPhi++;
          $request =  new RequestEntity(
            $firstPartOfPhi,
            self::$secondPartOfPhi,
            $year,
            $month,
            $day,
            [$this->createEntryThatBelongsToRequestWithIncrementalId(
              $firstPartOfPhi,
              self::$secondPartOfPhi,
              $year,
              $incrementalId
            )]
          );
          $arrayOfRequestsWithDateKeys[$year][$month][$day] = $request;
          $arrayOfRequests[] = $request;
          $incrementalId++;
        }
      }
    }
    return [$arrayOfRequests, $arrayOfRequestsWithDateKeys];
  }

  private function filterAllRequestsByDateInterval(array $arrayOfRequestsWithDateKeys, array $startDate, mixed $endDate = null)
  {
    $endDate = ($endDate === null) ? $startDate : $endDate;

    $allArraysInsideInterval = [];
    foreach ($arrayOfRequestsWithDateKeys as $year => $arrayBoundToYearKey) {
      if ($year >= $startDate[0] && $year <= $endDate[0]) {
        if (isset($startDate[1])) {
          foreach ($arrayBoundToYearKey as $month => $arrayBoundToYearAndMonthKey) {
            if ($month >= $startDate[1] && $month <= $endDate[1]) {
              if (isset($startDate[2])) {
                foreach ($arrayBoundToYearAndMonthKey as $day => $arrayBoundToYearMonthAndDayKey) {
                  if ($day >= $startDate[2] && $day <= $endDate[2]) {
                    $allArraysInsideInterval[] = $arrayBoundToYearMonthAndDayKey;
                  }
                }
              } else {
                $allArraysInsideInterval = array_merge($allArraysInsideInterval, [...$arrayBoundToYearAndMonthKey]);
              }
            }
          }
        } else {
          foreach ($arrayBoundToYearKey as $month => $arrayBoundToYearAndMonthKey) {
            $allArraysInsideInterval = array_merge($allArraysInsideInterval, [...$arrayBoundToYearAndMonthKey]);
          }
        }
      }
    }
    return $allArraysInsideInterval;
  }

  private function createEntryThatBelongsToRequestWithIncrementalId(int $firstPartOfPhi, int $secondPartOfPhi, int $year, int $lastId)
  {
    return new EntryEntity(
      $firstPartOfPhi,
      $secondPartOfPhi,
      $year,
      'nameNumberTest',
      'nameTest',
      'mainPartTest',
      100,
      'tem',
      99,
      1,
      'obs',
      $lastId + 1
    );
  }
}
