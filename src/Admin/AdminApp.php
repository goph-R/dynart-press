<?php

namespace Dynart\Press\Admin;

use Dynart\Micro\View;
use Dynart\Micro\WebApp;
use Dynart\Micro\Translation;
use Dynart\Micro\LocaleResolver;

class AdminApp extends WebApp {
    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->add(Translation::class);
        $this->add(LocaleResolver::class);
        $this->add(DashboardController::class);
        $this->addMiddleware(LocaleResolver::class);
    }

    public function init() {
        parent::init();
        $view = $this->get(View::class);
        $view->addFolder('admin', '~/admin/views');
    }
}