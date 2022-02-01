<?php

namespace fixtures;

use app\models\domains\request\RequestEntity;
use app\models\domains\request_entry\EntryEntity;
use PDO;


class EntryFixture
{
  private PDO $pdo;
  private PartFixture $partFixture;
  private ?int $consumableId = null;
  private int $lastPartId = 0;

  public function __construct(PDO $pdo, PartFixture $partFixture = null)
  {
    $this->pdo = $pdo;
    if (isset($partFixture)) {
      $this->partFixture = $partFixture;
    }
  }

  public function setConsumableIdForTest(?int $consumableId): void
  {
    $this->consumableId = $consumableId;
  }

  public function createEntriesWithPartsAndPersistToRequest(
    int $numberOfEntriesToCreate,
    RequestEntity $request,
    bool $withId = false,
    bool|int $startingFromOne = true
  ): array {
    $entries = $this->createEntries($numberOfEntriesToCreate, $withId, $startingFromOne);
    $this->persistEntries(
      $entries,
      ["firstPartOfPhi" => $request->getFirstPhi(), "year" => $request->getYear()]
    );

    $entriesWithParts = [];
    foreach ($entries as $entry) {
      $amountOfPartsToCreate = rand(1, 1);
      $parts = $this->partFixture->createParts($amountOfPartsToCreate, true, $this->lastPartId);
      $entry->addParts($parts);
      $this->partFixture->persistParts($parts, $entry->getId());
      $this->lastPartId = $this->pdo->lastInsertId();
      array_push($entriesWithParts, $entry);
    }
    return $entriesWithParts;
  }

  public function createEntriesWithoutPartsAndPersistToRequest(
    int $numberOfEntriesToCreate,
    RequestEntity $request,
    bool $withId = false,
    bool|int $startingFromOne = true
  ): array {
    $entriesWithoutParts = $this->createEntries($numberOfEntriesToCreate, $withId, $startingFromOne);
    $this->persistEntries(
      $entriesWithoutParts,
      ["firstPartOfPhi" => $request->getFirstPhi(), "year" => $request->getYear()]
    );
    return $entriesWithoutParts;
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
        $this->consumableId,
        $withId ? $id : null
      );

      array_push($entries, $newEntry);
    }

    return $entries;
  }

  public function persistEntries(array $entries, bool|array $requestPrimaryKeys = false)
  {
    foreach ($entries as $entry)
      $this->persistEntry($entry, $requestPrimaryKeys);
  }

  private function persistEntry(EntryEntity $entry, bool|array $requestPrimaryKeys)
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
    observations,
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
    :observations,
    :consumableId
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
      "consumableId" => $entry->getConsumableId()
    ]);
  }
}
