<?php

namespace Dynart\Press\Admin;

use Dynart\Micro\View;
use Dynart\Micro\WebApp;
use Dynart\Micro\Translation;
use Dynart\Micro\LocaleResolver;
use Dynart\Micro\AnnotationProcessor;
use Dynart\Micro\RouteAnnotation;

class AdminApp extends WebApp {
    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->add(Translation::class);
        $this->add(LocaleResolver::class);
        $this->add(Controller\DashboardController::class);
        $this->addMiddleware(LocaleResolver::class);
        $this->addMiddleware(AnnotationProcessor::class);
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