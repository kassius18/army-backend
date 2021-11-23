<?php

declare(strict_types=1);

namespace MyProject\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211121165909 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Populating the request table and the request_row table';
  }

  public function up(Schema $schema): void
  {
    $this->addSql(
      " INSERT INTO request(phi1, phi2, YEAR, MONTH, DAY)
 VALUES(15,2000,2021,05,15)"
    );

    $this->addSql(
      " INSERT INTO request_row(
    request_phi1,
    request_phi2,
    nameNumber,
    name,
    mainPart,
    amountOfOrder,
    unitOfOrder,
    reasonOfOrder,
    priorityOfOrder,
    observations,
    request_YEAR
)
VALUES(
    15,
    2000,
    '9S9972',
    'ΦΙΛΤΡΟ ΑΕΡΑ ΕΣΩΤ',
    'Π/Θ',
    1,
    'τεμ.',
    04,
    50,
    'Π/Θ CAT',
    2021
) "
    );
  }

  public function down(Schema $schema): void
  {
  }
}
