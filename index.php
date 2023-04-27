<?php

$start = microtime(true);

require_once __DIR__ . '/vendor/autoload.php';

\Dynart\Micro\App::run(new \Dynart\Press\PressApp(["config.ini.php"]));

echo microtime(true) - $start;
