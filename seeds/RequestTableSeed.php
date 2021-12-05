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
    15, 2000, 2021, 05, 15
);
SQL
    );

    /* $this->execute( */
    /*   <<<SQL */
    /* INSERT INTO request_row( */
    /* request_phi_first_part, */
    /* request_phi_second_part, */
    /* request_year, */
    /* name_number, */
    /* name, */
    /* main_part, */
    /* amount_of_order, */
    /* unit_of_order, */
    /* reason_of_order, */
    /* priority_of_order, */
    /* observations */
    /* ) */
    /* VALUES( */
    /* 15, */
    /* 2000, */
    /* 2021, */
    /* '9S9972', */
    /* 'ΦΙΛΤΡΟ ΑΕΡΑ ΕΣΩΤ', */
    /* 'Π/Θ', */
    /* 1, */
    /* 'τεμ.', */
    /* 04, */
    /* 50, */
    /* 'Π/Θ CAT' */
    /* ) */
    /* SQL */
    /* ); */
  }
}
