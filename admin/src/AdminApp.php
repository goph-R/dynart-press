<?php

namespace Dynart\Press\Admin;

use Dynart\Micro\AnnotationProcessor;
use Dynart\Micro\Database;
use Dynart\Micro\LocaleResolver;
use Dynart\Micro\RouteAnnotation;
use Dynart\Micro\Translation;
use Dynart\Micro\View;
use Dynart\Micro\WebApp;

class AdminApp extends WebApp {
    public function __construct(array $configPaths) {
        parent::__construct($configPaths);

        $this->add(Database::class);
        $this->add(Translation::class);
        $this->add(RouteAnnotation::class);
        $this->addMiddleware(LocaleResolver::class);
        $this->addMiddleware(AnnotationProcessor::class);

        $this->add(Controller\DashboardController::class);
    }

    public function init() {
        $annotations = $this->get(AnnotationProcessor::class);
        $annotations->addNamespace('Dynart\\Press\\Admin\\Controller');
        $annotations->add(RouteAnnotation::class);

        parent::init();

        $view = $this->get(View::class);
        $view->addFolder('admin', '~/admin/views');
    }
}