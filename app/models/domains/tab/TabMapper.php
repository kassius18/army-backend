<?php

namespace app\models\domains\tab;

use app\models\domains\part\PartFactory;
use PDO;

class TabMapper
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function findTabById(int $id)
  {
    $sql = <<<SQL
SELECT * FROM tab WHERE tab_id = :id;
SQL;
    $stm = $this->pdo->prepare($sql);
    $stm->execute([
      "id" => $id,
    ]);
    return TabFactory::createTabFromRecord($stm->fetch());
  }

  public function getAllTabs()
  {
    $sql = "SELECT * FROM tab";
    $statement = $this->pdo->prepare($sql);
    $statement->execute();
    $tabs = TabFactory::createManyTabsFromRecord($statement->fetchAll());
    return $tabs;
  }

  public function getAllNonEmptyTabsWithParts()
  {
    $sql = <<<SQL
SELECT DISTINCT tab.* , part.* FROM `request_row`
LEFT JOIN part ON `request_row`.`request_row_id` = `part`.entry_id
LEFT JOIN tab ON `tab`.tab_id = `request_row`.`consumable_tab_id`
WHERE `request_row`.`consumable_tab_id` IS NOT NULL
AND `part`.`part_id` IS NOT NULL
SQL;
    $stm = $this->pdo->prepare($sql);
    $stm->execute();
    return TabFactory::createTabsFromJOINRecord($stm->fetchAll());
  }

  public function getAllTabsWithParts()
  {
    $sql = <<<SQL
SELECT DISTINCT tab.* , part.* FROM `request_row`
LEFT JOIN part ON `request_row`.`request_row_id` = `part`.entry_id
LEFT JOIN tab ON `tab`.tab_id = `request_row`.`consumable_tab_id`
WHERE `request_row`.`consumable_tab_id` IS NOT NULL
AND `tab`.`tab_id` IS NOT NULL
SQL;
    $stm = $this->pdo->prepare($sql);
    $stm->execute();
    return TabFactory::createTabsFromJOINRecord($stm->fetchAll());
  }

  public function saveTab(TabEntity $tab): false|TabEntity
  {
    $sql = <<<SQL
 INSERT INTO tab(
    `tab_id`,
    `name`,
    `usage`,
    `observations`,
    `starting_total`
) VALUES (
    :id,
    :name,
    :usage,
    :observations,
    :startingTotal
);
SQL;

    $statement = $this->pdo->prepare($sql);
    if ($statement->execute([
      "name" => $tab->getName(),
      "usage" => $tab->getUsage(),
      "observations" => $tab->getObservations(),
      "startingTotal" => $tab->getStartingTotal(),
      "id" => $tab->getId()
    ])) {
      $lastId = $this->pdo->lastInsertId();
      return $this->findTabById($lastId);
    } else {
      return false;
    };
  }

  public function deleteTab(int $tabId): bool
  {
    $sql = <<<SQL
      DELETE FROM tab WHERE tab_id = :tabId;
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute(["tabId" => $tabId]);
  }

  public function updateTab(TabEntity $tab, int $tabId): false|TabEntity
  {
    $sql = <<<SQL
UPDATE tab 
SET 
    `name` = :name ,
    `usage`= :usage,
    `observations`= :observations,
    `starting_total`= :startingTotal
WHERE tab_id = :tabId;
SQL;
    $statement = $this->pdo->prepare($sql);
    if (
      $statement->execute([
        "name" => $tab->getName(),
        "usage" => $tab->getUsage(),
        "observations" => $tab->getObservations(),
        "startingTotal" => $tab->getStartingTotal(),
        "tabId" => $tabId
      ])
    ) {
      return $this->findTabById($tabId);
    } else {
      return false;
    };
  }

  public function getIdsOfNonEmptyTabs(): array
  {
    $sql = <<<SQL
SELECT  DISTINCT(consumable_tab_id) FROM `request_row`
INNER JOIN part
WHERE `request_row`.`request_row_id` = `part`.entry_id
AND `part`.entry_id = `request_row`.request_row_id
AND `request_row`.`consumable_tab_id` IS NOT NULL
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute([]);
    $records = $statement->fetchAll();
    $arrayOfIds = [];
    foreach ($records as $record) {
      array_push($arrayOfIds, $record["consumable_tab_id"]);
    }
    return $arrayOfIds;
  }

  public function findAllPartsThatBelongToTab(int $tabId): array
  {
    $sql = <<<SQL
SELECT
    *
FROM
    `request_row`
INNER JOIN part 
WHERE `request_row`.`consumable_tab_id` = :tabId
AND `part`.entry_id = `request_row`.request_row_id
;
SQL;
    $statement = $this->pdo->prepare($sql);
    $statement->execute([
      "tabId" => $tabId,
    ]);
    $records = $statement->fetchAll();
    $parts = PartFactory::createManyPartsFromRecord($records);
    return $parts;
  }
}
