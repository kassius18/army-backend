<?php


use Phinx\Seed\AbstractSeed;

class VehicleSeed extends AbstractSeed
{
  public function run()
  {
    $sql = <<<SQL
INSERT INTO vehicle(
    `vehicle_id` ,
    `plate` ,
    `vehicle_type`
)
VALUES(
    1, "plate1", "cadillac"
)
SQL;
    $this->execute($sql);
  }
}
