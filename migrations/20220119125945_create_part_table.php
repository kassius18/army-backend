<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePartTable extends AbstractMigration
{
  public function up(): void
  {
    $this->execute(
      'CREATE TABLE part(
    id INT(6) AUTO_INCREMENT PRIMARY KEY,
    entry_id INT(6),
    date_recieved VARCHAR(30),
    pie_number VARCHAR(30),
    amount_recieved INT(6),
    tab_used VARCHAR(30),
    date_used VARCHAR(30),
    amount_used VARCHAR(30),
    FOREIGN KEY(entry_id) REFERENCES request_row(id))'
    );
  }

  public function down(): void
  {
    $this->execute("DROP TABLE IF EXISTS part");
  }
}
