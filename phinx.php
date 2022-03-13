<?php

use Dotenv\Dotenv;

/* if (!isset($_ENV["dotenv"]) || !$_ENV["dotenv"]) { */
/*   var_dump(!isset($_ENV["dotenv"]) || $_ENV["dotenv"]); */
/*   var_dump("running"); */
/*   $dotenv = Dotenv::createImmutable("./app"); */
/*   $dotenv->load(); */
/* } */

/* $dotenv = Dotenv::createImmutable("./app"); */
/* $dotenv->load(); */

var_dump("we get here");

return
  [
    'paths' => [
      'migrations' => '%%PHINX_CONFIG_DIR%%/migrations',
      'seeds' => '%%PHINX_CONFIG_DIR%%/seeds'
    ],
    'environments' => [
      'default_migration_table' => 'phinxlog',
      'default_environment' => 'development',
      'production' => [
        'adapter' => $_ENV['production_driver'],
        'host' => $_ENV['production_host'],
        'name' => $_ENV['dbname_production'],
        'user' => $_ENV['production_username'],
        'pass' => $_ENV['production_password'],
      ],
      'development' => [
        'adapter' => $_ENV['driver'],
        'host' => $_ENV['host'],
        'name' => $_ENV['dbname_dev'],
        'user' => $_ENV['username'],
        'pass' => $_ENV['password'],
      ],
      'testing' => [
        'adapter' => $_ENV['driver'],
        'host' => $_ENV['host'],
        'name' => $_ENV['dbname_test'],
        'user' => $_ENV['username'],
        'pass' => $_ENV['password'],
      ],
      'online' => [
        'adapter' => $_ENV['driver_online'],
        'host' => $_ENV['host_online'],
        'name' => $_ENV['dbname_online'],
        'user' => $_ENV['username_online'],
        'pass' => $_ENV['password_online'],
      ]
    ],
    'version_order' => 'creation'
  ];
