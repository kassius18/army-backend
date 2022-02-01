<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateVehicleTable extends AbstractMigration
{
  public function up(): void
  {
    $sql = <<<SQL
 CREATE TABLE vehicle(
    `vehicle_id` INT(6) AUTO_INCREMENT PRIMARY KEY,
    `plate` VARCHAR(30),
    `vehicle_type` VARCHAR(30)
);
SQL;
    $this->execute($sql);
  }

  public function down(): void
  {
    $sql = <<<SQL
DROP TABLE IF EXISTS vehicle;
SQL;
    $this->execute($sql);
  }
}
