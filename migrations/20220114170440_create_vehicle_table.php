<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateVehicleTable extends AbstractMigration
{
  public function up(): void
  {
    $this->execute(
      'CREATE TABLE vehicle(
    id INT(6) AUTO_INCREMENT PRIMARY KEY,
    plate VARCHAR(30),
    vehicle_type VARCHAR(30))'
    );
  }

  public function down(): void
  {
    $this->execute("DROP TABLE IF EXISTS vehicle");
  }
}
