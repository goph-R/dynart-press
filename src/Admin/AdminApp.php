<?php

namespace Dynart\Press\Admin;

use Dynart\Micro\WebApp;
use Dynart\Micro\I18n\Translation;
use Dynart\Micro\I18n\LocaleResolver;

class AdminApp extends WebApp {
    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->add(Translation::class);
        $this->add(LocaleResolver::class);
        $this->add(DashboardController::class);
        $this->addMiddleware(LocaleResolver::class);
    }
}