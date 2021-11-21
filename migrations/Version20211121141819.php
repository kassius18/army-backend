<?php

declare(strict_types=1);

namespace MyProject\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211121141819 extends AbstractMigration
{
  public function getDescription(): string
  {
    return '';
  }

  public function up(Schema $schema): void
  {
    $this->addSql("CREATE TABLE request(
    id INT(6) AUTO_INCREMENT NOT NULL UNIQUE,
    phi INT(6) ,
    nameNumber VARCHAR(30),
    name VARCHAR(30),
    amountOfOrder INT(6),
    unitOfOrder VARCHAR(20),
    reasonOfOrder INT(6),
    priorityOfOrder INT(6),
    observations VARCHAR(30),
    year INT(6),
    month INT(6),
    day INT(6)
    PRIMARY KEY (phi, year))");
  }

  public function down(Schema $schema): void
  {
    $this->addSql("DROP TABLE IF EXISTS request");
  }
}
