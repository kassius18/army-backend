<?php

namespace fixtures;

use app\models\domains\part\PartEntity;
use PDO;

class PartFixture
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function createParts(int $numberOfPartsToCreate, bool $withId = false, bool|int $startingFromOne = true): array
  {
    $parts = [];

    $id = 0;

    if ($startingFromOne !== true) {
      $id = $startingFromOne;
    }

    for ($num = 1; $num <= $numberOfPartsToCreate; $num++) {
      $id++;
      $newPart = new PartEntity(
        uniqid(),
        uniqid(),
        rand(),
        uniqid(),
        uniqid(),
        rand(),
        $withId ? $id : null
      );

      array_push($parts, $newPart);
    }

    return $parts;
  }

  public function persistParts(array $parts, bool|int $entryId = false)
  {
    foreach ($parts as $part)
      $this->persistPart($part, $entryId);
  }

  private function persistPart(PartEntity $part, bool|int $entryId)
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
    $stm->execute([
      "entryId" => $entryId ?: null,
      "dateRecieved" => $part->getDateRecieved(),
      "pieNumber" => $part->getPieNumber(),
      "amountRecieved" => $part->getAmountRecieved(),
      "tabUsed" => $part->getTabUsed(),
      "dateUsed" => $part->getDateUsed(),
      "amountUsed" => $part->getAmountUsed()
    ]);
  }
}
