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

  public function saveTab(TabEntity $tab): false|TabEntity
  {
    $sql = <<<SQL
 INSERT INTO tab(
    `tab_id`,
    `name`,
    `usage`,
    `observations`
) VALUES (
    :id,
    :name,
    :usage,
    :observations
);
SQL;

    $statement = $this->pdo->prepare($sql);
    if ($statement->execute([
      "name" => $tab->getName(),
      "usage" => $tab->getUsage(),
      "observations" => $tab->getObservations(),
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

  public function updateTab(TabEntity $tab, int $tabId): bool
  {
    $sql = <<<SQL
UPDATE tab 
SET 
    `name` = :name ,
    `usage`= :usage,
    `observations`= :observations
WHERE tab_id = :tabId;
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      "name" => $tab->getName(),
      "usage" => $tab->getUsage(),
      "observations" => $tab->getObservations(),
      "tabId" => $tabId
    ]);
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
