<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTabTable extends AbstractMigration
{
  public function up(): void
  {
    $sql = <<<SQL
CREATE TABLE tab(
    `tab_id` INT(6) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `name` VARCHAR(30),
    `usage` VARCHAR(30),
    `observations` VARCHAR(30),
    `starting_total` INT(6) DEFAULT 0
);
SQL;
    $this->execute($sql);
  }

  public function down(): void
  {
    $sql = <<<SQL
DROP TABLE IF EXISTS tab
SQL;
    $this->execute($sql);
  }
}
