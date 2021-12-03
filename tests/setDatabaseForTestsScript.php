<?php

declare(strict_types=1);

$dotenv = Dotenv\Dotenv::createImmutable(APP_DIR);
$dotenv->load();

$dsn = $_ENV["driver_test"] . $_ENV["host_test"] . $_ENV["dbname_test"];
$username = $_ENV["username_test"];
$password = $_ENV["password_test"];
$options = [
  PDO::ATTR_EMULATE_PREPARES => false,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

return new PDO($dsn, $username, $password, $options);
