<?php

namespace fixtures;

use app\models\domains\request_entry\EntryEntity;
use PDO;


class EntryFixture
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function createEntries(int $numberOfEntriesToCreate, bool $withId = false, bool|int $startingFromOne = true): array
  {
    $entries = [];

    $id = 0;

    if ($startingFromOne !== true) {
      $id = $startingFromOne;
    }

    for ($num = 1; $num <= $numberOfEntriesToCreate; $num++) {
      $id++;
      $newEntry = new EntryEntity(
        uniqid(),
        uniqid(),
        uniqid(),
        rand(),
        uniqid(),
        rand(),
        rand(),
        uniqid(),
        $withId ? $id : null
      );

      array_push($entries, $newEntry);
    }

    return $entries;
  }

  public static function createEntryInputs(int $numberOfEntriesToCreate, bool $withId = false, bool|int $startingFromOne = true): array
  {
    $arrayOfInputs = [
      "nameNumber" => uniqid(),
      "name" => uniqid(),
      "mainPart" => uniqid(),
      "amountOfOrder" => rand(),
      "unitOfOrder" => uniqid(),
      "reasonOfOrder" => rand(),
      "priorityOfOrder" => rand(),
      "observations" => uniqid(),
    ];
    return $arrayOfInputs;
    /* $withId ? $id : null; */
  }

  public function persistEntries(array $entries, bool|array $requestPrimaryKeys = false)
  {
    foreach ($entries as $entry)
      $this->persistPart($entry, $requestPrimaryKeys);
  }

  private function persistPart(EntryEntity $entry, bool|array $requestPrimaryKeys)
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
      "firstPartOfPhi" =>  $requestPrimaryKeys ? $requestPrimaryKeys["firstPartOfPhi"] : null,
      "year" =>  $requestPrimaryKeys ? $requestPrimaryKeys["year"] : null,
      "nameNumber" => $entry->getNameNumber(),
      "name" => $entry->getName(),
      "mainPart" => $entry->getMainPart(),
      "amountOfOrder" => $entry->getAmountOfOrder(),
      "unitOfOrder" => $entry->getUnitOfOrder(),
      "reasonOfOrder" => $entry->getReasonOfOrder(),
      "priorityOfOrder" => $entry->getPriorityOfOrder(),
      "observations" => $entry->getObservations(),
    ]);
  }
}