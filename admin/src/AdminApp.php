<?php

namespace Dynart\Press\Admin;

use Dynart\Micro\WebApp;

use Dynart\Press\PressAppHelper;

class AdminApp extends WebApp {

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        PressAppHelper::create($this, true);
    }

    public function init() {
        parent::init();
        PressAppHelper::init(true);
    }

    public function process() {
        PressAppHelper::initPlugins(true);
        parent::process();
    }
}