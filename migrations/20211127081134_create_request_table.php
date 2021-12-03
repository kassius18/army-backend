<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRequestTable extends AbstractMigration
{
  public function up(): void
  {
    $this->execute("CREATE TABLE request(
    id INT(6) AUTO_INCREMENT NOT NULL UNIQUE,
    phi_first_part INT(6) ,
    phi_second_part INT(6) ,
    year INT(6),
    month INT(6),
    day INT(6),
    PRIMARY KEY (phi_first_part, phi_second_part, year))");
  }

  public function down(): void
  {
    $this->execute("DROP TABLE IF EXISTS request");
  }
}
