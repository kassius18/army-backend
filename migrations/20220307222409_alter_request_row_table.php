<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterRequestRowTable extends AbstractMigration
{
  public function up(): void
  {
    $sql = <<<SQL
 ALTER TABLE request_row
    MODIFY `name_number` VARCHAR(100),
    MODIFY `name` VARCHAR(100),
    MODIFY `main_part` VARCHAR(100),
    MODIFY `reason_of_order` VARCHAR(100),
    MODIFY `observations` VARCHAR(100)
SQL;
    $this->execute($sql);
  }

  public function down(): void
  {
    $sql = <<<SQL
 ALTER TABLE request_row
    MODIFY `name_number` VARCHAR(30),
    MODIFY `name` VARCHAR(30),
    MODIFY `main_part` VARCHAR(30),
    MODIFY `reason_of_order` VARCHAR(30),
    MODIFY `observations` VARCHAR(30)
SQL;
    $this->execute($sql);
  }
}
