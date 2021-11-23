<?php

declare(strict_types=1);

namespace MyProject\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211121164732 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Creating the request_row table';
  }

  public function up(Schema $schema): void
  {
    $this->addSql(
      "CREATE TABLE request_row(
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
    FOREIGN KEY(request_phi1,request_phi2, request_YEAR) REFERENCES request(phi1,phi2,year))"
    );
  }

  public function down(Schema $schema): void
  {
    $this->addSql("DROP TABLE IF EXISTS request_row");
  }
}
