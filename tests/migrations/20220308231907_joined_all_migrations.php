<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class JoinedAllMigrations extends AbstractMigration
{
  public function up(): void
  {
    $sql = <<<SQL
 CREATE TABLE IF NOT EXISTS vehicle(
    `vehicle_id` INT(6) AUTO_INCREMENT PRIMARY KEY,
    `plate` VARCHAR(100),
    `vehicle_type` VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS request(
    `request_id` INT(6) AUTO_INCREMENT NOT NULL UNIQUE,
    `phi_first_part` INT(6) ,
    `phi_second_part` INT(6) ,
    `year` INT(6),
    `month` INT(6),
    `day` INT(6),
    `request_vehicle_id` INT(6),
    PRIMARY KEY (`phi_first_part`, `year`),
  FOREIGN KEY fk_request_vehicle(`request_vehicle_id`) REFERENCES vehicle(vehicle_id)
);

CREATE TABLE IF NOT EXISTS tab(
    `tab_id` INT(6) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `name` VARCHAR(100),
    `usage` VARCHAR(100),
    `observations` VARCHAR(100),
    `starting_total` INT(6) DEFAULT 0
);

 CREATE TABLE IF NOT EXISTS request_row(
    `request_row_id` INT(6) AUTO_INCREMENT PRIMARY KEY,
    `request_phi_first_part` INT(6),
    `request_year` INT(6),
    `name_number` VARCHAR(100),
    `name` VARCHAR(100),
    `main_part` VARCHAR(100),
    `amount_of_order` INT(6),
    `unit_of_order` VARCHAR(20),
    `reason_of_order` VARCHAR(100),
    `priority_of_order` INT(6),
    `consumable_tab_id` INT(6),

    FOREIGN KEY(`request_phi_first_part`, `request_year`) 
      REFERENCES request(`phi_first_part`,`year`)
      ON DELETE CASCADE ON UPDATE CASCADE
);

 CREATE TABLE IF NOT EXISTS part(
    `part_id` INT(6) AUTO_INCREMENT PRIMARY KEY,
    `entry_id` INT(6),
    `date_recieved` VARCHAR(100),
    `pie_number` VARCHAR(100),
    `amount_recieved` INT(6),
    `tab_used` VARCHAR(100),
    `date_used` VARCHAR(100),
    `amount_used` VARCHAR(100),
    FOREIGN KEY (`entry_id`) REFERENCES request_row(`request_row_id`)
    ON DELETE CASCADE);

SQL;
    $this->execute($sql);
  }

  public function down(): void
  {
    $sql = <<<SQL
DROP TABLE IF EXISTS part;
DROP TABLE IF EXISTS request_row;
DROP TABLE IF EXISTS request;
DROP TABLE IF EXISTS vehicle;
DROP TABLE IF EXISTS tab;
SQL;
    $this->execute($sql);
  }
}
