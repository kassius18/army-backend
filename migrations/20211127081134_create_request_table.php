<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRequestTable extends AbstractMigration
{
  public function up(): void
  {
    $this->execute("CREATE TABLE request(
    id INT(6) AUTO_INCREMENT NOT NULL UNIQUE,
    phi1 INT(6) ,
    phi2 INT(6) ,
    nameNumber VARCHAR(30),
    name VARCHAR(30),
    amountOfOrder INT(6),
    unitOfOrder VARCHAR(20),
    reasonOfOrder INT(6),
    priorityOfOrder INT(6),
    observations VARCHAR(30),
    year INT(6),
    month INT(6),
    day INT(6),
    PRIMARY KEY (phi1, phi2, year))");
  }

  public function down(): void
  {
    $this->execute("DROP TABLE IF EXISTS request");
  }
}
