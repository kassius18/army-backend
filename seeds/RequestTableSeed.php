<?php


use Phinx\Seed\AbstractSeed;

class RequestTableSeed extends AbstractSeed
{
  public function run()
  {
    $this->execute(
      <<<SQL
INSERT INTO request(
    phi_first_part,
    phi_second_part,
    YEAR,
    MONTH,
    DAY
)
VALUES(
    1, 2, 2021, 05, 15
    1, 2, 2022, 05, 15
    1, 3, 2021, 05, 15
    2, 2, 2021, 05, 15
);
SQL
    );

    $this->execute(
      <<<SQL
INSERT INTO request_row(
    request_phi_first_part,
    request_phi_second_part,
    nameNumber,
    NAME,
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
)
SQL
    );
  }
}
