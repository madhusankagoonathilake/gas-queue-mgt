<?php

require_once '../common/config.php';
$dsn = 'mysql:host=' . CONFIG['db']['host'] . ';port=' . CONFIG['db']['port'] . ';dbname=' . CONFIG['db']['database'] . ';charset=utf8mb4';
$dbh = new \PDO($dsn, CONFIG['db']['username'], CONFIG['db']['password']);
$dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
