<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRequestRowTable extends AbstractMigration
{
  public function up(): void
  {
    $this->execute(
      'CREATE TABLE request_row(
    id INT(6) AUTO_INCREMENT PRIMARY KEY,
    request_phi1 INT(6),
    request_phi2 INT(6),
    nameNumber VARCHAR(30),
    name VARCHAR(30),
    mainPart VARCHAR(30),
    amountOfOrder INT(6),
    unitOfOrder VARCHAR(20),
    reasonOfOrder INT(6),
    priorityOfOrder INT(6),
    observations VARCHAR(30),
    request_YEAR INT(6),
    FOREIGN KEY(request_phi1,request_phi2, request_YEAR) REFERENCES request(phi1,phi2,year))'
    );
  }

  public function down(): void
  {
    $this->execute("DROP TABLE IF EXISTS request_row");
  }
}
