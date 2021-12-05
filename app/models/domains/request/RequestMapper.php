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
)
ON DUPLICATE KEY
UPDATE
    MONTH =
VALUES(MONTH), DAY =
VALUES(DAY)
SQL;

    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      'firstPartOfPhi' => $request->getFirstPhi(),
      'secondPartOfPhi' => $request->getSecondPhi(),
      'year' => $request->getYear(),
      'month' => $request->getMonth(),
      'day' => $request->getDay()
    ]);
  }

  public function findOneByPhiAndYear(int $firstPartOfPhi, int $secondPartOfPhi, int $year): RequestEntity
  {
    $sql = <<<SQL
SELECT
    *
FROM
    request
WHERE
    phi_first_part = :firstPartOfPhi AND phi_second_part = :secondPartOfPhi AND year = :year
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute(
      [
        'firstPartOfPhi' => $firstPartOfPhi,
        'secondPartOfPhi' => $secondPartOfPhi,
        'year' => $year
      ]
    );
    $result = $statement->fetchAll();
    return RequestFactory::createRequestEntityFromRecord($result[0]);
  }

  public function saveManyRecords(array $arrayOfRequest): void
  {
    foreach ($arrayOfRequest as $key => $request) {
      $this->saveRequest($request);
    }
  }

  public function findManyByFullPhi(int $firstPartOfPhi, int $secondPartOfPhi): array
  {
    $sql = <<<SQL
SELECT * FROM request WHERE 
phi_first_part = :firstPartOfPhi
AND
phi_second_part = :secondPartOfPhi
ORDER BY year
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute([
      'firstPartOfPhi' => $firstPartOfPhi,
      'secondPartOfPhi' => $secondPartOfPhi
    ]);
    $allRecords = $statement->fetchAll();
    return RequestFactory::createRequestEntityFromArrayOfRecords($allRecords);
  }

  public function findAllByDateInterval(int $startYear, int $endYear = null): array
  {
    $endYear = $endYear ?: $startYear;
    $sql = <<<SQL
SELECT * FROM request WHERE
year >= :startYear
AND
year <= :endYear
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute([
      'startYear' => $startYear,
      'endYear' => $endYear
    ]);
    $allRecords = $statement->fetchAll();
    return RequestFactory::createRequestEntityFromArrayOfRecords($allRecords);
  }
}
