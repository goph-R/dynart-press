<?php

namespace Dynart\Press;

use Dynart\Micro\WebApp;

class PressApp extends WebApp {

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        PressAppHelper::create($this, false);
    }

    public function init() {
        parent::init();
        PressAppHelper::init(false);
    }

    public function process() {
        PressAppHelper::initPlugins(false);
        parent::process();
    }
}