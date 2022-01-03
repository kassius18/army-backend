<?php

declare(strict_types=1);

use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

$dotenv = Dotenv\Dotenv::createImmutable(APP_DIR);

$dotenv->load();

$dsn = $_ENV["driver"] . ":host=" . $_ENV["host"] . ";dbname=" . $_ENV["dbname_test"];
$username = $_ENV["username"];
$password = $_ENV["password"];
$options = [
  PDO::ATTR_EMULATE_PREPARES => false,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

return new PDO($dsn, $username, $password, $options);
