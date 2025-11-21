<?php
// db.php
$config = require __DIR__ . '/config.php';
$dbconf = $config['db'];

$mysqli = new mysqli($dbconf['host'], $dbconf['user'], $dbconf['pass'], $dbconf['name'], $dbconf['port']);
if ($mysqli->connect_errno) {
    die("DB connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");
