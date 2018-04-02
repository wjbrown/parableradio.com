<?php

define('ROOT', dirname(__DIR__));
define('DS', '/');

require ROOT . DS . '/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$db = \ParagonIE\EasyDB\Factory::create(
    'mysql:host=' . getenv('DBHOST') . ';dbname=' . getenv('DBNAME'),
    getenv('DBUSER'),
    getenv('DBPASS')
);

$polly = new \Aws\Polly\PollyClient([
    'version'     => '2016-06-10',
    'profile'     => 'default',
    'region'      => 'us-east-1',
]);