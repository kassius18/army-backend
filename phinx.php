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
        'name' => 'papadhmhtrakhs_production',
        'user' => $_ENV['username'],
        'pass' => $_ENV['password'],
      ],
      'development' => [
        'adapter' => $_ENV['driver'],
        'host' => $_ENV['host'],
        'name' => 'papadhmhtrakhs_dev',
        'user' => $_ENV['username'],
        'pass' => $_ENV['password'],
      ],
      'testing' => [
        'adapter' => $_ENV['driver'],
        'host' => $_ENV['host'],
        'name' => 'papadhmhtrakhs_test',
        'user' => $_ENV['username'],
        'pass' => $_ENV['password'],
      ]
    ],
    'version_order' => 'creation'
  ];
