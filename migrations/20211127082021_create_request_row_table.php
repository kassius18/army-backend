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
    request_year INT(6),
    name_number VARCHAR(30),
    name VARCHAR(30),
    main_part VARCHAR(30),
    amount_of_order INT(6),
    unit_of_order VARCHAR(20),
    reason_of_order INT(6),
    priority_of_order INT(6),
    observations VARCHAR(30),
    FOREIGN KEY(request_phi_first_part, request_YEAR) REFERENCES request(phi_first_part, year))'
    );
  }

  public function down(): void
  {
    $this->execute("DROP TABLE IF EXISTS request_row");
  }
}
