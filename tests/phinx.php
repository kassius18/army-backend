<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable("./app");
$dotenv->load();

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
        'adapter' => $_ENV['driver'],
        'host' => $_ENV['host'],
        'name' => $_ENV['dbname_production'],
        'user' => $_ENV['username'],
        'pass' => $_ENV['password'],
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
