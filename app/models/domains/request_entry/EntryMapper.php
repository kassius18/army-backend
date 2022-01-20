<?php

namespace app\models\domains\request_entry;

use PDO;

class EntryMapper
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function saveEntry(EntryEntity $entry): bool
  {
    $sql = <<<SQL
INSERT INTO request_row(
    request_phi_first_part,
    request_year,
    name_number,
    name,
    main_part,
    amount_of_order,
    unit_of_order,
    reason_of_order,
    priority_of_order,
    observations
)
VALUES(
    :firstPartOfPhi,
    :year,
    :nameNumber,
    :name,
    :mainPart,
    :amountOfOrder,
    :unitOfOrder,
    :reasonOfOrder,
    :priorityOfOrder,
    :observations
)
SQL;

    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      'firstPartOfPhi' => $entry->getFirstPhi(),
      'year' => $entry->getYear(),
      'nameNumber' => $entry->getNameNumber(),
      'name' => $entry->getName(),
      'mainPart' => $entry->getMainPart(),
      'amountOfOrder' => $entry->getAmountOfOrder(),
      'unitOfOrder' => $entry->getUnitOfOrder(),
      'reasonOfOrder' => $entry->getReasonOfOrder(),
      'priorityOfOrder' => $entry->getPriorityOfOrder(),
      'observations' => $entry->getObservations(),
    ]);
  }

  public function updateEntry(EntryEntity $entry)
  {
    $sql = <<<SQL
UPDATE request_row
SET
request_phi_first_part = :firstPartOfPhi,
request_year = :year,
name_number = :nameNumber,
name = :name,
main_part = :mainPart,
amount_of_order = :amountOfOrder,
unit_of_order = :unitOfOrder,
reason_of_order = :reasonOfOrder,
priority_of_order = :priorityOfOrder,
observations= :observations
WHERE id = :id
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      'firstPartOfPhi' => $entry->getFirstPhi(),
      'year' => $entry->getYear(),
      'nameNumber' => $entry->getNameNumber(),
      'name' => $entry->getName(),
      'mainPart' => $entry->getMainPart(),
      'amountOfOrder' => $entry->getAmountOfOrder(),
      'unitOfOrder' => $entry->getUnitOfOrder(),
      'reasonOfOrder' => $entry->getReasonOfOrder(),
      'priorityOfOrder' => $entry->getPriorityOfOrder(),
      'observations' => $entry->getObservations(),
      'id' => $entry->getId(),
    ]);
    return null;
  }


  public function findAllByPhiAndYear(int $firstPartOfPhi, int $year): array
  {
    $sql = <<<SQL
SELECT
    *
FROM
    request_row
WHERE
    request_phi_first_part = :firstPartOfPhi AND request_year = :year
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute(
      [
        'firstPartOfPhi' => $firstPartOfPhi,
        'year' => $year
      ]
    );
    $result = $statement->fetchAll();
    return EntryFactory::createEntryFromArrayOfRecords($result);
  }

  public function saveManyEntries(array $arrayOfRequests): void
  {
    foreach ($arrayOfRequests as $request) {
      $this->saveEntry($request);
    }
  }

  public function deleteOneByFullPhiYearAndId(int $firstPartOfPhi, int $year, int $id)
  {
    $sql = <<<SQL
DELETE FROM
    request_row
WHERE
    request_phi_first_part = :firstPartOfPhi AND request_year = :year AND id = :id
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute(
      [
        'firstPartOfPhi' => $firstPartOfPhi,
        'year' => $year,
        'id' => $id
      ]
    );
  }
}
