<?php

namespace Dynart\Press\Admin;

use Dynart\Micro\Database;
use Dynart\Micro\Database\PdoBuilder;
use Dynart\Micro\Database\MariaDatabase;
use Dynart\Micro\Middleware\LocaleResolver;
use Dynart\Micro\Middleware\AnnotationProcessor;
use Dynart\Micro\Annotation\RouteAnnotation;
use Dynart\Micro\Translation;
use Dynart\Micro\View;
use Dynart\Micro\WebApp;

use Dynart\Press\Service\PluginRepository;
use Dynart\Press\Service\PluginService;

class AdminApp extends WebApp {

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);

        $this->add(PdoBuilder::class);
        $this->add(Database::class, MariaDatabase::class);

        $this->add(Translation::class);
        $this->addMiddleware(LocaleResolver::class);

        $this->add(RouteAnnotation::class);
        $this->addMiddleware(AnnotationProcessor::class);

        $this->add(PluginRepository::class);
        $this->addMiddleware(PluginService::class);

        $this->add(Controller\DashboardController::class);

        $annotations = $this->get(AnnotationProcessor::class);
        $annotations->addNamespace('Dynart\\Press\\Admin\\Controller');
        $annotations->add(RouteAnnotation::class);
    }

    public function init() {
        parent::init();
        try {
            $translation = $this->get(Translation::class);
            $translation->add('admin', '~/admin/translations');

            $view = $this->get(View::class);
            $view->addFolder('admin', '~/admin/views');

            $pluginManager = $this->get(PluginService::class);
            $pluginManager->init();
            $pluginManager->adminInit();
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
}