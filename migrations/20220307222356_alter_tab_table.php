<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterTabTable extends AbstractMigration
{
  public function up(): void
  {
    $sql = <<<SQL
 ALTER TABLE tab
   MODIFY `name` VARCHAR(100),
   MODIFY `usage` VARCHAR(100),
   MODIFY `observations` VARCHAR(100)
SQL;
    $this->execute($sql);
  }

  public function down(): void
  {
    $sql = <<<SQL
 ALTER TABLE tab
   MODIFY `name` VARCHAR(30),
   MODIFY `usage` VARCHAR(30),
   MODIFY `observations` VARCHAR(30)
SQL;
    $this->execute($sql);
  }
}
