<?php

namespace Dynart\Press;

use Dynart\Micro\WebApp;

class PressApp extends WebApp {

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        PressAppSetup::create($this, false);
    }

    public function init() {
        parent::init();
        PressAppSetup::init($this, false);
    }

    public function process() {
        PressAppSetup::initPlugins($this, false);
        parent::process();
    }
}