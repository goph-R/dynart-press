<?php

require_once __DIR__ . '/../vendor/autoload.php';

\Dynart\Micro\App::run(new \Dynart\Photos\Admin\AdminApp(["../config.ini.php", "admin.config.ini.php"]));