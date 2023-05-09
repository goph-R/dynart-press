<?php

if (http_response_code() !== false) {
    die("Run from CLI!");
}

require_once __DIR__ . '/vendor/autoload.php';

function runCli() { // TODO
    $configPaths = ["config.ini.php"];
    if (in_array('-admin', $_SERVER['argv'])) {
        $configPaths[] = "admin/admin.config.ini.php";
    }
    \Dynart\Micro\App::run(new \Dynart\Press\PressCliApp($configPaths));
}

runCli();