<?php

namespace app\models\domains\request;

use app\models\domains\request_entry\EntryMapper;
use PDO;

class RequestMapper
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function saveRequest(RequestEntity $request, EntryMapper $entryMapper): void
  {
    $sql = <<<SQL
INSERT INTO request(
    phi_first_part,
    phi_second_part,
    YEAR,
    MONTH,
    DAY
)
VALUES(
    :firstPartOfPhi,
    :secondPartOfPhi,
    :year,
    :month,
    :day
);
SQL;

    $statement = $this->pdo->prepare($sql);
    $statement->execute([
      'firstPartOfPhi' => $request->getFirstPhi(),
      'secondPartOfPhi' => $request->getSecondPhi(),
      'year' => $request->getYear(),
      'month' => $request->getMonth(),
      'day' => $request->getDay()
    ]);

    $entryMapper->saveManyEntries($request->getEntries());
  }

  public function findOneByPhiAndYear(int $firstPartOfPhi, int $year): RequestEntity
  {
    $sql = <<<SQL
SELECT
    *
FROM
    request
INNER JOIN request_row 
ON 
request_row.request_phi_first_part = request.phi_first_part
AND
request_row.request_year = request.year
WHERE
    phi_first_part = :firstPartOfPhi AND year = :year
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute(
      [
        'firstPartOfPhi' => $firstPartOfPhi,
        'year' => $year
      ]
    );
    $result = $statement->fetchAll();
    return RequestFactory::createRequestEntityFromRecord($result);
  }

  public function findManyByPhi(int $firstPartOfPhi): array
  {
    $sql = <<<SQL
SELECT * FROM request
INNER JOIN request_row 
ON 
request_row.request_phi_first_part = request.phi_first_part
AND 
request_row.request_year = request.year
WHERE 
phi_first_part = :firstPartOfPhi
ORDER BY year
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute([
      'firstPartOfPhi' => $firstPartOfPhi,
    ]);
    $allRecords = $statement->fetchAll(PDO::FETCH_GROUP);
    return RequestFactory::createRequestEntityFromArrayOfRecords($allRecords);
  }

  public function findAllByDateInterval(array $startDate, array $endDate = null): array
  {
    $endDate = $endDate ?: $startDate;

    $sql = <<<SQL
SELECT * FROM request
INNER JOIN request_row 
ON 
request_row.request_phi_first_part = request.phi_first_part
AND
request_row.request_year = request.year
 WHERE
YEAR >= :startYear
AND
YEAR <= :endYear
SQL;

    $preparedStatementValues = [
      'startYear' => $startDate[0],
      'endYear' => $endDate[0]
    ];

    if (isset($startDate[1])) {
      $sql .= <<<SQL

AND 
MONTH >= :startMonth
AND
MONTH <= :endMonth
SQL;
      $preparedStatementValues['startMonth'] = $startDate[1];
      $preparedStatementValues['endMonth'] = $endDate[1];
    }
    if (isset($startDate[2])) {
      $sql .= <<<SQL

AND 
DAY >= :startDay
AND
DAY <= :endDay
SQL;
      $preparedStatementValues['startDay'] = $startDate[2];
      $preparedStatementValues['endDay'] = $endDate[2];
    }

    $statement = $this->pdo->prepare($sql);
    $statement->execute($preparedStatementValues);
    $allRecords = $statement->fetchAll(PDO::FETCH_GROUP);
    return RequestFactory::createRequestEntityFromArrayOfRecords($allRecords);
  }

  public function updateRequest(RequestEntity $request)
  {
    $sql = <<<SQL
UPDATE request
    SET MONTH = :month, DAY = :day
SQL;

    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      'month' => $request->getMonth(),
      'day' => $request->getDay()
    ]);
  }
}
