<?php

namespace app\models\domains\part;

use PDO;

class PartMapper
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function savePartToEntry(PartEntity $part, int $entryId): false|PartEntity
  {
    $sql = <<<SQL
INSERT INTO part (
    `entry_id`,
    `date_recieved`,
    `pie_number`,
    `amount_recieved`,
    `tab_used`,
    `date_used`,
    `amount_used`
) VALUE (
    :entryId,
    :dateRecieved,
    :pieNumber,
    :amountRecieved,
    :tabUsed,
    :dateUsed,
    :amountUsed
);
SQL;
    $stm = $this->pdo->prepare($sql);
    if ($stm->execute([
      "entryId" => $entryId,
      "dateRecieved" => $part->getDateRecieved(),
      "pieNumber" => $part->getPieNumber(),
      "amountRecieved" => $part->getAmountRecieved(),
      "tabUsed" => $part->getTabUsed(),
      "dateUsed" => $part->getDateUsed(),
      "amountUsed" => $part->getAmountUsed()
    ])) {
      $lastId = $this->pdo->lastInsertId();
      return $this->findPartById($lastId);
    } else {
      return false;
    };
  }

  public function findAllPartsByEntryId(int $entryId)
  {
    $sql = <<<SQL
SELECT * FROM part WHERE entry_id = :entryId;
SQL;
    $stm = $this->pdo->prepare($sql);
    $stm->execute([
      "entryId" => $entryId,
    ]);
    return PartFactory::createManyPartsFromRecord($stm->fetchAll());
  }

  public function findPartById(int $id)
  {
    $sql = <<<SQL
SELECT * FROM part WHERE part_id = :id;
SQL;
    $stm = $this->pdo->prepare($sql);
    $stm->execute([
      "id" => $id,
    ]);
    return PartFactory::createPartFromRecord($stm->fetch());
  }

  public function deletePartById(int $id): bool
  {
    $sql = <<<SQL
DELETE FROM part WHERE part_id = :id;
SQL;
    $stm = $this->pdo->prepare($sql);
    return $stm->execute([
      "id" => $id,
    ]);
  }

  public function updatePartById(PartEntity $editedPart, int $id): false|PartEntity
  {
    $sql = <<<SQL
UPDATE part
SET 
    date_recieved = :dateRecieved,
    pie_number = :pieNumber,
    amount_recieved = :amountRecieved,
    tab_used = :tabUsed,
    date_used = :dateUsed,
    amount_used = :amountUsed
WHERE part_id = :id 
;
SQL;
    $stm = $this->pdo->prepare($sql);
    if ($stm->execute([
      "id" => $id,
      "dateRecieved" => $editedPart->getDateRecieved(),
      "pieNumber" => $editedPart->getPieNumber(),
      "amountRecieved" => $editedPart->getAmountRecieved(),
      "tabUsed" => $editedPart->getTabUsed(),
      "dateUsed" => $editedPart->getDateUsed(),
      "amountUsed" => $editedPart->getAmountUsed()
    ])) {
      return $this->findPartById($id);
    } else {
      return false;
    };
  }
}
