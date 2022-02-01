<?php

namespace app\models\domains\request;

use PDO;

class RequestMapper
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function saveRequest(RequestEntity $request): false|RequestEntity
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
    if ($statement->execute([
      'firstPartOfPhi' => $request->getFirstPhi(),
      'secondPartOfPhi' => $request->getSecondPhi(),
      'year' => $request->getYear(),
      'month' => $request->getMonth(),
      'day' => $request->getDay()
    ])) {
      $lastId = $this->pdo->lastInsertId();
      return $this->findOneById($lastId);
    } else {
      return false;
    };
  }

  public function findOneByPhiAndYear(int $firstPartOfPhi, int $year): RequestEntity
  {
    $sql = <<<SQL
SELECT
    *
FROM
    request
LEFT JOIN request_row ON request_row.request_phi_first_part = request.phi_first_part AND request_row.request_year = request.year
LEFT JOIN part ON part.entry_id = request_row.request_row_id
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
    $request = RequestFactory::createRequestsFromJOINRecord($result);
    return $request[0];
  }

  public function findOneById(int $id): ?RequestEntity
  {
    $sql = <<<SQL
SELECT * FROM
    request
LEFT JOIN request_row ON request_row.request_phi_first_part = request.phi_first_part AND request_row.request_year = request.year
LEFT JOIN part ON part.entry_id = request_row.request_row_id
WHERE
    `request`.request_id = :id
ORDER BY
    `request`.request_id
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute([
      'id' => $id,
    ]);
    $records = $statement->fetchAll();
    $request = RequestFactory::createRequestsFromJOINRecord($records);
    return $request[0];
  }

  public function findManyByPhi(int $firstPartOfPhi): array
  {
    $sql = <<<SQL
SELECT * FROM 
    request
LEFT JOIN request_row ON request_row.request_phi_first_part = request.phi_first_part AND request_row.request_year = request.year
LEFT JOIN part ON part.entry_id = request_row.request_row_id
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
    return RequestFactory::createRequestsFromJOINRecord($allRecords);
  }

  public function findAllByDateInterval(array $startDate, array $endDate = null): array
  {
    $endDate = $endDate ?: $startDate;

    $sql = <<<SQL
SELECT * FROM 
    request
LEFT JOIN request_row ON request_row.request_phi_first_part = request.phi_first_part AND request_row.request_year = request.year
LEFT JOIN part ON part.entry_id = request_row.request_row_id
WHERE
    year >= :startYear
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
    request.year, request.month, request.day, request.request_id
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute($preparedStatementValues);
    $allRecords = $statement->fetchAll();
    return RequestFactory::createRequestsFromJOINRecord($allRecords);
  }

  public function updateRequest(RequestEntity $request, int $requestId): false|RequestEntity
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
    `request_id` = :id
SQL;

    $statement = $this->pdo->prepare($sql);
    if ($statement->execute([
      'firstPartOfPhi' => $request->getFirstPhi(),
      'secondPartOfPhi' => $request->getSecondPhi(),
      'year' => $request->getYear(),
      'month' => $request->getMonth(),
      'day' => $request->getDay(),
      'id' => $requestId
    ])) {
      return $this->findOneById($requestId);
    } else {
      return false;
    }
  }

  public function deleteRequestById(int $requestId)
  {
    $sql = <<<SQL
DELETE FROM 
    request
WHERE
    request_id = :id
;
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute(["id" => $requestId]);
  }
}
