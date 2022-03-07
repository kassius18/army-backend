<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterVehicleTable extends AbstractMigration
{
  public function up(): void
  {
    $sql = <<<SQL
 ALTER TABLE vehicle
    MODIFY `plate` VARCHAR(100),
    MODIFY `vehicle_type` VARCHAR(100)
SQL;
    $this->execute($sql);
  }

  public function down(): void
  {
    $sql = <<<SQL
 ALTER TABLE vehicle
    MODIFY `plate` VARCHAR(30),
    MODIFY `vehicle_type` VARCHAR(30)
SQL;
    $this->execute($sql);
  }
}
