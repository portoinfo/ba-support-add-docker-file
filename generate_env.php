<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "APP_KEY=" . getenv('APP_KEY') . "\n";
echo "DB_HOST=" . getenv('DB_HOST') . "\n";
echo "DB_DATABASE=" . getenv('DB_DATABASE') . "\n";
echo "DB_USERNAME=" . getenv('DB_USERNAME') . "\n";
echo "DB_PASSWORD=" . getenv('DB_PASSWORD') . "\n";
