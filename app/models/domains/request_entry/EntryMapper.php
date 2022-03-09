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

  public function saveEntryToRequest(EntryEntity $entry, array $requestPrimaryKeys): false|EntryEntity
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
    consumable_tab_id
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
    :consumableId
)
SQL;

    $statement = $this->pdo->prepare($sql);
    if ($statement->execute([
      "firstPartOfPhi" => $requestPrimaryKeys["firstPartOfPhi"],
      "year" => $requestPrimaryKeys["year"],
      "nameNumber" => $entry->getNameNumber(),
      "name" => $entry->getName(),
      "mainPart" => $entry->getMainPart(),
      "amountOfOrder" => $entry->getAmountOfOrder(),
      "unitOfOrder" => $entry->getUnitOfOrder(),
      "reasonOfOrder" => $entry->getReasonOfOrder(),
      "priorityOfOrder" => $entry->getPriorityOfOrder(),
      "consumableId" => $entry->getConsumableId()
    ])) {
      $lastId = $this->pdo->lastInsertId();
      return $this->findEntryById($lastId);
    } else {
      return false;
    }
  }

  public function updateEntryById(EntryEntity $entry, int $id): false|EntryEntity
  {
    $sql = <<<SQL
UPDATE request_row
SET
name_number = :nameNumber,
name = :name,
main_part = :mainPart,
amount_of_order = :amountOfOrder,
unit_of_order = :unitOfOrder,
reason_of_order = :reasonOfOrder,
priority_of_order = :priorityOfOrder,
consumable_tab_id= :consumable
WHERE 
request_row_id = :id;
SQL;
    $statement = $this->pdo->prepare($sql);
    if ($statement->execute([
      "nameNumber" => $entry->getNameNumber(),
      "name" => $entry->getName(),
      "mainPart" => $entry->getMainPart(),
      "amountOfOrder" => $entry->getAmountOfOrder(),
      "unitOfOrder" => $entry->getUnitOfOrder(),
      "reasonOfOrder" => $entry->getReasonOfOrder(),
      "priorityOfOrder" => $entry->getPriorityOfOrder(),
      "consumable" => $entry->getConsumableId(),
      "id" => $id,
    ])) {
      return $this->findEntryById($id);
    } else {
      return false;
    }
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
        "firstPartOfPhi" => $firstPartOfPhi,
        "year" => $year
      ]
    );
    $result = $statement->fetchAll();
    return EntryFactory::createManyEntriesFromRecord($result);
  }

  public function findEntryById(int $entryId)
  {
    $sql = <<<SQL
SELECT
    *
FROM
    request_row
WHERE 
    request_row_id = :id;
SQL;
    $stm = $this->pdo->prepare($sql);
    $stm->execute(
      ["id" => $entryId]
    );
    return EntryFactory::createEntryFromRecord($stm->fetch());
  }

  public function deleteEntryById(int $id): bool
  {
    $sql = <<<SQL
DELETE FROM
    request_row
WHERE
    request_row_id = :id;
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute(
      [
        "id" => $id
      ]
    );
  }
}
