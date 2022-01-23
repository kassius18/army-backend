<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRequestTable extends AbstractMigration
{
  public function up(): void
  {
    $sql = <<<SQL
CREATE TABLE request(
    `id` INT(6) AUTO_INCREMENT NOT NULL UNIQUE,
    `phi_first_part` INT(6) ,
    `phi_second_part` INT(6) ,
    `year` INT(6),
    `month` INT(6),
    `day` INT(6),
    PRIMARY KEY (`phi_first_part`, `year`)
);
SQL;
    $this->execute($sql);
  }

  public function down(): void
  {
    $sql = <<<SQL
DROP TABLE IF EXISTS request;
SQL;
    $this->execute($sql);
  }
}
