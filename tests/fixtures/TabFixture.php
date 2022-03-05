<?php

namespace fixtures;

use app\models\domains\tab\TabEntity;
use PDO;

class TabFixture
{
  private PDO $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function createTabs(int $numberOfTabsToCreate, bool|int $startingFromOne = true): array
  {
    $tabs = [];

    $id = 0;

    if ($startingFromOne !== true) {
      $id = $startingFromOne;
    }

    for ($num = 1; $num <= $numberOfTabsToCreate; $num++) {
      $id++;
      $newTab = new TabEntity(
        uniqid(),
        uniqid(),
        uniqid(),
        rand(0, 100),
        $id
      );

      array_push($tabs, $newTab);
    }
    return $tabs;
  }

  public function persistTabs(array $tabs)
  {
    foreach ($tabs as $tab)
      $this->persistTab($tab);
  }

  private function persistTab(TabEntity $tab)
  {
    $sql = <<<SQL
INSERT INTO tab(
    `tab_id`,
    `name`,
    `usage`,
    `observations`,
    `starting_total`
)
VALUES(
    :id,
    :name,
    :usage,
    :observations
    :startingTotal
);
SQL;

    $statement = $this->pdo->prepare($sql);
    return $statement->execute([
      "id" =>  $tab->getId(),
      "name" =>  $tab->getName(),
      "usage" => $tab->getUsage(),
      "observations" => $tab->getObservations(),
      "startingTotal" => $tab->getStartingTotal()
    ]);
  }
}
