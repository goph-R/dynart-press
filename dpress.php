<?php

if (http_response_code() !== false) {
    die("Run from CLI!");
}

require_once __DIR__ . '/vendor/autoload.php';
\Dynart\Micro\App::run(new \Dynart\Press\PressCliApp(["config.ini.php"]));
