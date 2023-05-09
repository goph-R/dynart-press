<?php

namespace Dynart\Press\Admin;

use Dynart\Micro\WebApp;

use Dynart\Press\PressAppSetup;

class AdminApp extends WebApp {

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        PressAppSetup::create($this, true);
    }

    public function init() {
        parent::init();
        PressAppSetup::init($this, true);
    }

    public function process() {
        PressAppSetup::initPlugins($this, true);
        parent::process();
    }
}