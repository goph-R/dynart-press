<?php

require_once __DIR__ . '/../vendor/autoload.php';

\Dynart\Micro\App::run(new \Dynart\Press\Admin\AdminApp(["../config.ini.php", "admin.config.ini.php"]));