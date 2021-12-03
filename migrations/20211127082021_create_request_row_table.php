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
    request_phi_first_part INT(6),
    request_phi_second_part INT(6),
    nameNumber VARCHAR(30),
    name VARCHAR(30),
    mainPart VARCHAR(30),
    amountOfOrder INT(6),
    unitOfOrder VARCHAR(20),
    reasonOfOrder INT(6),
    priorityOfOrder INT(6),
    observations VARCHAR(30),
    request_YEAR INT(6),
    FOREIGN KEY(request_phi_first_part,request_phi_second_part, request_YEAR) REFERENCES request(phi_first_part,phi_second_part,year))'
    );
  }

  public function down(): void
  {
    $this->execute("DROP TABLE IF EXISTS request_row");
  }
}
