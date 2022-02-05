<?php


use Phinx\Seed\AbstractSeed;

class TabSeed extends AbstractSeed
{
  public function run()
  {
    $sql = <<<SQL
INSERT INTO tab(
   `tab_id` ,
   `name` ,
   `usage`,
   `observations` ,
   `starting_total` 
)
VALUES(
    1, "tabOne", "some usage", "some observations", 0
),(
    2, "tabTwo", "some other Usage", "some observations 2", 5
)
SQL;
    $this->execute($sql);
  }
}
