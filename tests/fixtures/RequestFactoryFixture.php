<?php

namespace fixtures;

use app\models\domains\request\RequestFactory;

class RequestFactoryFixture
{

  public function testEntriesBelongToRequestsFixture()
  {
    $requestFromDbRecord = [
      [
        'phi_first_part' => 1,
        'phi_second_part' => 2,
        'year' => 3,
        'month' => 4,
        'day' => 5,
        'request_phi_first_part' => 1,
        'request_phi_second_part' => 2,
        'request_year' => 3,
        'name_number' => 'nameNumber',
        'name' => 'name',
        'main_part' => 'mainPart',
        'amount_of_order' => 4,
        'unit_of_order' => "unit",
        'reason_of_order' => 5,
        'priority_of_order' => 6,
        'observations' => 'obs',
        'id' => 7
      ],
      [
        'phi_first_part' => 11,
        'phi_second_part' => 2,
        'year' => 3,
        'month' => 4,
        'day' => 5,
        'request_phi_first_part' => 1,
        'request_phi_second_part' => 2,
        'request_year' => 3,
        'name_number' => 'nameNumber',
        'name' => 'name',
        'main_part' => 'mainPart',
        'amount_of_order' => 4,
        'unit_of_order' => "unit",
        'reason_of_order' => 5,
        'priority_of_order' => 6,
        'observations' => 'obs',
        'id' => 7
      ],
      [
        'phi_first_part' => 1,
        'phi_second_part' => 11,
        'year' => 3,
        'month' => 4,
        'day' => 5,
        'request_phi_first_part' => 1,
        'request_phi_second_part' => 2,
        'request_year' => 3,
        'name_number' => 'nameNumber',
        'name' => 'name',
        'main_part' => 'mainPart',
        'amount_of_order' => 4,
        'unit_of_order' => "unit",
        'reason_of_order' => 5,
        'priority_of_order' => 6,
        'observations' => 'obs',
        'id' => 7
      ],
      [
        'phi_first_part' => 11,
        'phi_second_part' => 11,
        'year' => 3,
        'month' => 4,
        'day' => 5,
        'request_phi_first_part' => 1,
        'request_phi_second_part' => 2,
        'request_year' => 3,
        'name_number' => 'nameNumber',
        'name' => 'name',
        'main_part' => 'mainPart',
        'amount_of_order' => 4,
        'unit_of_order' => "unit",
        'reason_of_order' => 5,
        'priority_of_order' => 6,
        'observations' => 'obs',
        'id' => 7
      ],
      [
        'phi_first_part' => 11,
        'phi_second_part' => 11,
        'year' => 1000,
        'month' => 4,
        'day' => 5,
        'request_phi_first_part' => 1,
        'request_phi_second_part' => 2,
        'request_year' => 3,
        'name_number' => 'nameNumber',
        'name' => 'name',
        'main_part' => 'mainPart',
        'amount_of_order' => 4,
        'unit_of_order' => "unit",
        'reason_of_order' => 5,
        'priority_of_order' => 6,
        'observations' => 'obs',
        'id' => 7
      ],
      [
        'phi_first_part' => 1,
        'phi_second_part' => 2,
        'year' => 1000,
        'month' => 4,
        'day' => 5,
        'request_phi_first_part' => 1,
        'request_phi_second_part' => 2,
        'request_year' => 3,
        'name_number' => 'nameNumber',
        'name' => 'name',
        'main_part' => 'mainPart',
        'amount_of_order' => 4,
        'unit_of_order' => "unit",
        'reason_of_order' => 5,
        'priority_of_order' => 6,
        'observations' => 'obs',
        'id' => 7
      ]
    ];

    return RequestFactory::createRequestEntityFromRecord(
      $requestFromDbRecord
    );
  }
}
