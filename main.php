<?php
require_once 'vendor/autoload.php';
$config = yaml_parse_file(__DIR__ . '/config.yaml');
$dbConfig = $config['database'];

use App\Controllers\Action as Controller;

try {
    $db = new PDO($dbConfig['dsn'], $dbConfig['user'], $dbConfig['pass']);
} catch (PDOException $e) {
    print 'Could not connect to DB: ' . $e->getMessage();
    die();
}

$controller = new Controller($db);

$controller->run();
