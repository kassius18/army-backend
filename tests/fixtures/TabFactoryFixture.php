<?php

use app\models\domains\part\PartFactory;
use app\models\domains\tab\TabEntity;
use app\models\domains\tab\TabFactory;
use app\models\domains\tab_entry\EntryFactory;

$recordsFromJoin = [
  [
    "tab_id" => 1,
    "name" => "tabOneTest",
    "usage" => "someUsage",
    "observations" => "someObs",
    "starting_total" => 0,
    "part_id" => 1,
    "entry_id" => 1,
    "pie_number" => "asd",
    "date_recieved" => "1-2-2021",
    "amount_recieved" => 100,
    "tab_used" => "as",
    "date_used" => "1-3-2021",
    "amount_used" => 3,
  ], [
    "tab_id" => 1,
    "name" => "tabOneTest",
    "usage" => "someUsage",
    "observations" => "someObs",
    "starting_total" => 0,
    "part_id" => 2,
    "entry_id" => 2,
    "pie_number" => "asd",
    "date_recieved" => "1-2-2021",
    "amount_recieved" => 100,
    "tab_used" => "as",
    "date_used" => "1-3-2021",
    "amount_used" => 3,
  ], [
    "tab_id" => 1,
    "name" => "tabOneTest",
    "usage" => "someUsage",
    "observations" => "someObs",
    "starting_total" => 0,
    "part_id" => 3,
    "entry_id" => 1,
    "pie_number" => "asd",
    "date_recieved" => null,
    "amount_recieved" => 0,
    "tab_used" => null,
    "date_used" => null,
    "amount_used" => null,
  ], [
    "tab_id" => 2,
    "name" => "tabOneTest",
    "usage" => "someUsage",
    "observations" => "someObs",
    "starting_total" => 0,
    "part_id" => 4,
    "entry_id" => 9,
    "pie_number" => "asd",
    "date_recieved" => null,
    "amount_recieved" => 0,
    "tab_used" => null,
    "date_used" => null,
    "amount_used" => null,
  ]
];

$tab = TabFactory::createTabFromRecord($recordsFromJoin[0]);
$part = PartFactory::createPartFromRecord($recordsFromJoin[0]);
$tab->addParts([$part]);
$part = PartFactory::createPartFromRecord($recordsFromJoin[1]);
$tab->addParts([$part]);
$part = PartFactory::createPartFromRecord($recordsFromJoin[2]);
$tab->addParts([$part]);

$secondTab = TabFactory::createTabFromRecord($recordsFromJoin[3]);
$part = PartFactory::createPartFromRecord($recordsFromJoin[3]);
$secondTab->addParts([$part]);

return [[$tab, $secondTab], $recordsFromJoin];
