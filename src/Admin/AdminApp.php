<?php

namespace Dynart\Press\Admin;

use Dynart\Micro\WebApp;
use Dynart\Press\ImageRepository;
use Dynart\Press\ImageService;
use Dynart\Press\UserService;

class AdminApp extends WebApp {

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->add(ImageService::class);
        $this->add(ImageRepository::class);
        $this->add(DashboardController::class);
        $this->add(UserService::class);
    }

    public function init() {
        parent::init();
        //$this->router->add('*', [DashboardController::class, 'index']);
        /*$this->router->add('/api/test', [DashboardController::class, 'test']);*/
    }
}