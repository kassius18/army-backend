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

  public function saveRequest(RequestEntity $request): bool
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
    return  $statement->execute([
      'firstPartOfPhi' => $request->getFirstPhi(),
      'secondPartOfPhi' => $request->getSecondPhi(),
      'year' => $request->getYear(),
      'month' => $request->getMonth(),
      'day' => $request->getDay()
    ]);
  }

  public function findOneByPhiAndYear(int $firstPartOfPhi, int $year): RequestEntity
  {
    $sql = <<<SQL
SELECT
    *
FROM
    request
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
    $result = $statement->fetch();
    return RequestFactory::createRequestFromRecord($result);
  }

  public function findOneById(int $id): ?RequestEntity
  {
    $sql = <<<SQL
SELECT * FROM 
    request
WHERE
    id = :id
ORDER BY
    id
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute([
      'id' => $id,
    ]);
    $record = $statement->fetch();
    return RequestFactory::createRequestFromRecord($record);
  }

  public function findManyByPhi(int $firstPartOfPhi): array
  {
    $sql = <<<SQL
SELECT * FROM 
    request
WHERE
    phi_first_part = :firstPartOfPhi
ORDER BY
    year;
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute([
      'firstPartOfPhi' => $firstPartOfPhi,
    ]);
    $allRecords = $statement->fetchAll();
    return RequestFactory::createManyRequestsFromRecord($allRecords);
  }

  public function findAllByDateInterval(array $startDate, array $endDate = null): array
  {
    $endDate = $endDate ?: $startDate;

    $sql = <<<SQL
SELECT * FROM 
    request
WHERE
    YEAR >= :startYear
AND
    year <= :endYear
SQL;

    $preparedStatementValues = [
      'startYear' => $startDate[0],
      'endYear' => $endDate[0]
    ];

    if (isset($startDate[1])) {
      $sql .= <<<SQL

AND 
    month >= :startMonth
AND
    month <= :endMonth
SQL;
      $preparedStatementValues['startMonth'] = $startDate[1];
      $preparedStatementValues['endMonth'] = $endDate[1];
    }
    if (isset($startDate[2])) {
      $sql .= <<<SQL

AND 
    day >= :startDay
AND
    day <= :endDay
SQL;
      $preparedStatementValues['startDay'] = $startDate[2];
      $preparedStatementValues['endDay'] = $endDate[2];
    }

    $sql .= <<<SQL

ORDER BY
    year, month, day, id
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute($preparedStatementValues);
    $allRecords = $statement->fetchAll();
    return RequestFactory::createManyRequestsFromRecord($allRecords);
  }

  public function updateRequest(RequestEntity $request, int $requestId)
  {
    $sql = <<<SQL
UPDATE 
    request
SET 
    `phi_first_part` = :firstPartOfPhi,
    `phi_second_part` = :secondPartOfPhi,
    `year` = :year,
    `month` = :month,
    `day` = :day
WHERE
    `id` = :id
SQL;

    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      'firstPartOfPhi' => $request->getFirstPhi(),
      'secondPartOfPhi' => $request->getSecondPhi(),
      'year' => $request->getYear(),
      'month' => $request->getMonth(),
      'day' => $request->getDay(),
      'id' => $requestId
    ]);
  }

  public function deleteRequestById(int $requestId)
  {
    $sql = <<<SQL
DELETE FROM 
    request
WHERE
    id = :id
;
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute(["id" => $requestId]);
  }
}
