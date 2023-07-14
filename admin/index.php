<?php

require_once __DIR__ . '/../vendor/autoload.php';

\Dynart\Micro\Micro::run(new \Dynart\Press\Admin\AdminApp(["../config.ini.php", "admin.config.ini.php"]));