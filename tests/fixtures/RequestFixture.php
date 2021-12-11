<?php

namespace fixtures;

use app\models\domains\request\RequestEntity;
use PDOStatement;

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
    $this->commonRequest = new RequestEntity(
      self::$firstPartOfPhi,
      self::$secondPartOfPhi,
      self::$year,
      self::$month,
      self::$day
    );
  }

  public function getFindingByFullPhiFixture(): array
  {
    $requestWithSamePhi = new RequestEntity(
      self::$firstPartOfPhi,
      self::$secondPartOfPhi,
      self::$differentYear,
      self::$month,
      self::$day
    );

    $requestWithDifferentePhi = new RequestEntity(
      self::$differentFirstPartOfPhi,
      self::$secondPartOfPhi,
      self::$differentYear,
      self::$month,
      self::$day
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
    $requestWithDifferentDay = new RequestEntity(
      self::$firstPartOfPhi,
      self::$secondPartOfPhi,
      self::$year,
      self::$month,
      self::$differentDay
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

  private function persistDataSet(array $arrayOfRequestObjects): PDOStatement
  {
    $sql = "INSERT INTO request(
    phi_first_part,
    phi_second_part,
    YEAR,
    MONTH,
    DAY
)VALUES";
    foreach ($arrayOfRequestObjects as $key => $requestEntity) {
      $sql .= "(
{$requestEntity->getFirstPhi()},
{$requestEntity->getSecondPhi()},
{$requestEntity->getYear()},
{$requestEntity->getMonth()},
{$requestEntity->getDay()}
),";
    }
    $sqlWithoutEndComma = substr($sql, 0, -1);
    return self::$pdo->query($sqlWithoutEndComma);
  }

  private function createRequestsForIntervalTestAndFilterByDate(array $startDate, mixed $endDate = null)
  {
    [$arrayOfRequests, $arrayOfRequestsWithDateKeys] = $this->createRequestsForFindingByInterval();
    $allArraysInsideInterval = $this->filterAllRequestsByDateInterval($arrayOfRequestsWithDateKeys, $startDate, $endDate);

    $this->persistDataSet($arrayOfRequests);
    return [
      "requests" => $allArraysInsideInterval
    ];
  }

  private function createRequestsForFindingByInterval(): array
  {
    $arrayOfRequestsWithDateKeys = [];
    $arrayOfRequests = [];
    $firstPartOfPhi = 0;
    for ($year = 2000; $year < 2003; $year++) {
      for ($month = 1; $month < 4; $month++) {
        for ($day = 20; $day < 23; $day++) {
          $firstPartOfPhi++;
          $request =  new RequestEntity(
            $firstPartOfPhi,
            self::$secondPartOfPhi,
            $year,
            $month,
            $day
          );
          $arrayOfRequestsWithDateKeys[$year][$month][$day] = $request;
          $arrayOfRequests[] = $request;
          return [$arrayOfRequests, $arrayOfRequestsWithDateKeys];
        }
      }
    }
  }

  private function filterAllRequestsByDateInterval(array $arrayOfRequestsWithDateKeys, array $startDate, mixed $endDate = null)
  {
    $negativeValueSoTheIfStatementsFailIfKeyNotSet = -1;
    $endDate = $endDate ?: $startDate;
    $startDate[0] = isset($startDate[0]) ? $startDate[0] : $negativeValueSoTheIfStatementsFailIfKeyNotSet;
    $startDate[1] = isset($startDate[1]) ? $startDate[1] : $negativeValueSoTheIfStatementsFailIfKeyNotSet;
    $startDate[2] = isset($startDate[2]) ? $startDate[2] : $negativeValueSoTheIfStatementsFailIfKeyNotSet;
    $endDate[0] = isset($endDate[0]) ?: $negativeValueSoTheIfStatementsFailIfKeyNotSet;
    $endDate[1] = isset($endDate[1]) ?: $negativeValueSoTheIfStatementsFailIfKeyNotSet;
    $endDate[2] = isset($endDate[2]) ?: $negativeValueSoTheIfStatementsFailIfKeyNotSet;

    $allArraysInsideInterval = [];
    foreach ($arrayOfRequestsWithDateKeys as $year => $arrayBoundToYearKey) {
      if ($year >= $startDate[0] && $year <= $endDate[0]) {
        foreach ($arrayBoundToYearKey as $month => $arrayBoundToYearAndMonthKey) {
          if ($month >= $startDate[1] && $month <= $endDate[1]) {
            foreach ($arrayBoundToYearAndMonthKey as $day => $arrayBoundToYearMonthAndDayKey) {
              if ($day >= $startDate[2] && $day <= $endDate[2]) {
                $allArraysInsideInterval[] = $arrayBoundToYearMonthAndDayKey;
              }
            }
          }
        }
      }
    }
    return $allArraysInsideInterval;
  }
}
