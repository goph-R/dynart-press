<?php

$str = "sql query !with '!with' \"!with\" !with";
$newStr = preg_replace('/(?<!["\'])\!with\b/', '', $str);
echo $newStr;

/*
require_once __DIR__ . '/vendor/autoload.php';

use Dynart\Micro\Config;
use Dynart\Micro\Logger;
use Dynart\Micro\Database\PdoBuilder;
use Dynart\Micro\Database\MariaDatabase;

$config = new Config();
$config->load('config.ini.php');

$logger = new Logger($config);

$pdoBuilder = new PdoBuilder();
$db = new MariaDatabase($config, $logger, $pdoBuilder);
try {
    $db->query("select 1");
} catch (PDOException $e) {
    echo "Can't connect to database.\n";
    echo "The error message was: ".$e->getMessage()."\n";
}

$db->fetch(file_get_contents());
*/