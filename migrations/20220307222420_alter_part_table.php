<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AlterPartTable extends AbstractMigration
{
  public function up(): void
  {
    $sql = <<<SQL
 ALTER TABLE part
   MODIFY `date_recieved` VARCHAR(100),
   MODIFY `pie_number` VARCHAR(100),
   MODIFY `tab_used` VARCHAR(100),
   MODIFY `date_used` VARCHAR(100),
   MODIFY `amount_used` VARCHAR(100)
SQL;
    $this->execute($sql);
  }

  public function down(): void
  {
    $sql = <<<SQL
 ALTER TABLE part
   MODIFY `date_recieved` VARCHAR(30),
   MODIFY `pie_number` VARCHAR(30),
   MODIFY `tab_used` VARCHAR(30),
   MODIFY `date_used` VARCHAR(30),
   MODIFY `amount_used` VARCHAR(30)
SQL;
    $this->execute($sql);
  }
}
