<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterRequestTableAddVehicle extends AbstractMigration
{
  public function up(): void
  {
    $sql = <<<SQL
ALTER TABLE request
  ADD COLUMN request_vehicle_id INT(6),
  ADD FOREIGN KEY fk_request_vehicle(`request_vehicle_id`) REFERENCES vehicle(vehicle_id)
SQL;
    $this->execute($sql);
  }

  public function down(): void
  {
    $sql = <<<SQL
ALTER TABLE request
  DROP FOREIGN KEY `fk_request_vehicle`,  
  DROP COLUMN request_vehicle_id  
SQL;

    $this->execute($sql);
  }
}
