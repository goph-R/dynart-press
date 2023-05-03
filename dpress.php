<?php

if (!php_sapi_name()) {
    die("You can run this only from CLI!");
}

require_once __DIR__ . '/vendor/autoload.php';
\Dynart\Micro\App::run(new \Dynart\Press\PressCliApp(["config.ini.php"]));
