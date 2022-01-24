<?php

namespace app\models\domains\tab;

use PDO;

class TabMapper
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function getAllTabs()
  {
    $sql = "SELECT * FROM tab";
    $statement = $this->pdo->prepare($sql);
    $statement->execute();
    $tabs = TabFactory::createManyTabsFromRecord($statement->fetchAll());
    return $tabs;
  }

  public function saveTab(TabEntity $tab): bool
  {
    $sql = <<<SQL
 INSERT INTO tab(
    `id`,
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
    return $statement->execute([
      "name" => $tab->getName(),
      "usage" => $tab->getUsage(),
      "observations" => $tab->getObservations(),
      "id" => $tab->getId()
    ]);
  }

  public function deleteTab(int $tabId): bool
  {
    $sql = <<<SQL
      DELETE FROM tab WHERE id = :tabId;
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
WHERE id = :tabId;
SQL;
    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      "name" => $tab->getName(),
      "usage" => $tab->getUsage(),
      "observations" => $tab->getObservations(),
      "tabId" => $tabId
    ]);
  }
}