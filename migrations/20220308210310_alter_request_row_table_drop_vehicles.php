<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterRequestRowTableDropVehicles extends AbstractMigration
{
  public function up(): void
  {
    $sql = <<<SQL
ALTER TABLE request_row
  DROP COLUMN observations
SQL;
    $this->execute($sql);
  }

  public function down(): void
  {
    $sql = <<<SQL
ALTER TABLE request_row
  ADD COLUMN observations VARCHAR(100)
SQL;
    $this->execute($sql);
  }
}
