<?php

use app\models\domains\part\PartFactory;
use app\models\domains\request\RequestFactory;
use app\models\domains\request_entry\EntryFactory;

$recordsFromJoin = [
  [
    'request_id' => 1,
    'phi_first_part' => 1855294779,
    'phi_second_part' => 1584092229,
    'year' => 1100369292,
    'month' => 1390753353,
    'day' => 898893964,
    'request_vehicle_id' => 10,
    'request_row_id' => 1,
    'request_phi_first_part' => 1855294779,
    'request_year' => 1100369292,
    'name_number' => "61f80b54dfb7f",
    'name' => "61f80b54dfb82",
    'main_part' => "61f80b54dfb85",
    'amount_of_order' => 203099896,
    'unit_of_order' => "61f80b54dfb89",
    'reason_of_order' => 1469856502,
    'priority_of_order' => 1192362177,
    'observations' => "61f80b54dfb8e",
    'consumable_tab_id' => NULL,
    'part_id' => 1,
    'entry_id' => 1,
    'date_recieved' => "61f80b54e0495",
    'pie_number' => "61f80b54e0498",
    'amount_recieved' => 1138732270,
    'tab_used' => "61f80b54e049c",
    'date_used' => "61f80b54e049f",
    'amount_used' => "1484184687",
  ],
  [
    'request_id' => 1,
    'phi_first_part' => 1855294779,
    'phi_second_part' => 1584092229,
    'year' => 1100369292,
    'month' => 1390753353,
    'day' => 898893964,
    'request_vehicle_id' => 15,
    'request_row_id' => 1,
    'request_phi_first_part' => 1855294779,
    'request_year' => 1100369292,
    'name_number' => "61f80b54dfb7f",
    'name' => "61f80b54dfb82",
    'main_part' => "61f80b54dfb85",
    'amount_of_order' => 203099896,
    'unit_of_order' => "61f80b54dfb89",
    'reason_of_order' => 1469856502,
    'priority_of_order' => 1192362177,
    'observations' => "61f80b54dfb8e",
    'consumable_tab_id' => NULL,
    'part_id' => 2,
    'entry_id' => 1,
    'date_recieved' => "61f80b54e152b",
    'pie_number' => "61f80b54e152f",
    'amount_recieved' => 47068837,
    'tab_used' => "61f80b54e1533",
    'date_used' => "61f80b54e1535",
    'amount_used' => "2051057966",
  ], [
    'request_id' => 1,
    'phi_first_part' => 1855294779,
    'phi_second_part' => 1584092229,
    'year' => 1100369292,
    'month' => 1390753353,
    'day' => 898893964,
    'request_vehicle_id' => 12,
    'request_row_id' => 2,
    'request_phi_first_part' => 1855294779,
    'request_year' => 1100369292,
    'name_number' => "61f80b54dfb7f",
    'name' => "61f80b54dfb82",
    'main_part' => "61f80b54dfb85",
    'amount_of_order' => 203099896,
    'unit_of_order' => "61f80b54dfb89",
    'reason_of_order' => 1469856502,
    'priority_of_order' => 1192362177,
    'observations' => "61f80b54dfb8e",
    'consumable_tab_id' => null,
    'part_id' => 3,
    'entry_id' => 2,
    'date_recieved' => "61f80b54e153e",
    'pie_number' => "61f80b54e1540",
    'amount_recieved' => 1818884197,
    'tab_used' => "61f80b54e1544",
    'date_used' => "61f80b54e1546",
    'amount_used' => "1482217923",
  ], [
    'request_id' => 1,
    'phi_first_part' => 1855294779,
    'phi_second_part' => 1584092229,
    'year' => 1100369292,
    'month' => 1390753353,
    'day' => 898893964,
    'request_vehicle_id' => 2,
    'request_row_id' => 3,
    'request_phi_first_part' => 185080809,
    'request_year' => 11001908,
    'name_number' => "61f80b54dfb7f",
    'name' => "61f80b54dfb82",
    'main_part' => "61f80b54dfb85",
    'amount_of_order' => 203096876,
    'unit_of_order' => "61f80b54dfb89",
    'reason_of_order' => 1469856502,
    'priority_of_order' => 1192362177,
    'observations' => "61f80b54dfb8e",
    'consumable_tab_id' => null,
    'part_id' => null,
    'entry_id' => null,
    'date_recieved' => null,
    'pie_number' => null,
    'amount_recieved' => null,
    'tab_used' => null,
    'date_used' => null,
    'amount_used' => null,
  ], [
    'request_id' => 2,
    'phi_first_part' => 1855294779,
    'phi_second_part' => 1584092229,
    'year' => 1100369292,
    'month' => 1390753353,
    'day' => 898893964,
    'request_vehicle_id' => 250,
    'request_row_id' => 4,
    'request_phi_first_part' => 1855294779,
    'request_year' => 1100369292,
    'name_number' => "61f80b54dfb7f",
    'name' => "61f80b54dfb82",
    'main_part' => "61f80b54dfb85",
    'amount_of_order' => 203099896,
    'unit_of_order' => "61f80b54dfb89",
    'reason_of_order' => 1469856502,
    'priority_of_order' => 1192362177,
    'observations' => "61f80b54dfb8e",
    'consumable_tab_id' => null,
    'part_id' => 4,
    'entry_id' => 4,
    'date_recieved' => "61f80b54e153e",
    'pie_number' => "61f80b54e1540",
    'amount_recieved' => 1818884197,
    'tab_used' => "61f80b54e1544",
    'date_used' => "61f80b54e1546",
    'amount_used' => "1482217923",
  ]
];

$request = RequestFactory::createRequestFromRecord($recordsFromJoin[0]);
$entry = EntryFactory::createEntryFromRecord($recordsFromJoin[0]);
$request->addEntries([$entry]);
$part = PartFactory::createPartFromRecord($recordsFromJoin[0]);
$entry->addParts([$part]);

$part = PartFactory::createPartFromRecord($recordsFromJoin[1]);
$entry->addParts([$part]);

$entry = EntryFactory::createEntryFromRecord($recordsFromJoin[2]);
$request->addEntries([$entry]);
$part = PartFactory::createPartFromRecord($recordsFromJoin[2]);
$entry->addParts([$part]);

$entry = EntryFactory::createEntryFromRecord($recordsFromJoin[3]);
$request->addEntries([$entry]);

$secondRequest = RequestFactory::createRequestFromRecord($recordsFromJoin[4]);
$entry = EntryFactory::createEntryFromRecord($recordsFromJoin[4]);
$secondRequest->addEntries([$entry]);
$part = PartFactory::createPartFromRecord($recordsFromJoin[4]);
$entry->addParts([$part]);

return [[$request, $secondRequest], $recordsFromJoin];
