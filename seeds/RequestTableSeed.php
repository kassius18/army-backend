<?php


use Phinx\Seed\AbstractSeed;

class RequestTableSeed extends AbstractSeed
{
  public function run()
  {
    $this->execute(
      " INSERT INTO request(phi1, phi2, YEAR, MONTH, DAY)
 VALUES(15,2000,2021,05,15)"
    );

    $this->execute(
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
}
