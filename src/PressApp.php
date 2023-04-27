<?php

namespace Dynart\Press;

use Dynart\Micro\Database;
use Dynart\Micro\Database\PdoBuilder;
use Dynart\Micro\Database\MariaDatabase;
use Dynart\Micro\Middleware\LocaleResolver;
use Dynart\Micro\Middleware\AnnotationProcessor;
use Dynart\Micro\Annotation\RouteAnnotation;
use Dynart\Micro\Translation;
use Dynart\Micro\WebApp;
use Dynart\Press\Service\PluginManager;
use Dynart\Press\Service\PluginRepository;


class PressApp extends WebApp {

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);

        $this->add(PdoBuilder::class);
        $this->add(Database::class, MariaDatabase::class);

        $this->add(Translation::class);
        $this->addMiddleware(LocaleResolver::class);

        $this->add(RouteAnnotation::class);
        $this->addMiddleware(AnnotationProcessor::class);

        $this->add(PluginRepository::class);
        $this->addMiddleware(PluginManager::class);

        $this->add(Service\ImageService::class);
        $this->add(Service\ImageRepository::class);
        $this->add(Controller\HomeController::class);

        /** @var PluginManager $pluginManager */
        $pluginManager = $this->get(PluginManager::class);
        $pluginManager->init();

        /** @var AnnotationProcessor $annotations */
        $annotations = $this->get(AnnotationProcessor::class);
        $annotations->addNamespace('Dynart\\Press\\Controller');
        $annotations->add(RouteAnnotation::class);
    }

    public function init() {
        parent::init();
        /** @var Translation $translation */
        $translation = $this->get(Translation::class);
        $translation->add('press', '~/translations');
    }
}