<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRequestRowTable extends AbstractMigration
{
  public function up(): void
  {
    $sql = <<<SQL
 CREATE TABLE request_row(
    `request_row_id` INT(6) AUTO_INCREMENT PRIMARY KEY,
    `request_phi_first_part` INT(6),
    `request_year` INT(6),
    `name_number` VARCHAR(30),
    `name` VARCHAR(30),
    `main_part` VARCHAR(30),
    `amount_of_order` INT(6),
    `unit_of_order` VARCHAR(20),
    `reason_of_order` VARCHAR(30),
    `priority_of_order` INT(6),
    `observations` VARCHAR(30),
    `consumable_tab_id` INT(6),

    FOREIGN KEY(`request_phi_first_part`, `request_year`) 
      REFERENCES request(`phi_first_part`,`year`)
      ON DELETE CASCADE ON UPDATE CASCADE,

    FOREIGN KEY (`consumable_tab_id`) 
      REFERENCES tab(`tab_id`)
);
SQL;
    $this->execute($sql);
  }

  public function down(): void
  {
    $sql = <<<SQL
DROP TABLE IF EXISTS request_row
SQL;
    $this->execute($sql);
  }
}
