<?php

namespace Dynart\Press;

use Dynart\Micro\WebApp;

require_once 'views/functions.php';

class PressApp extends WebApp {

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->add(ImageService::class);
        $this->add(ImageRepository::class);
        $this->add(HomeController::class);
    }

    public function init() {
        parent::init();
        $this->router->add('/', [HomeController::class, 'index']);
    }
}