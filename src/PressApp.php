<?php

namespace Dynart\Press;

use Dynart\Micro\LocaleResolver;
use Dynart\Micro\AnnotationProcessor;
use Dynart\Micro\RouteAnnotation;
use Dynart\Micro\Translation;
use Dynart\Micro\Database;
use Dynart\Micro\WebApp;

class PressApp extends WebApp {

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->add(Database::class);
        $this->add(Translation::class);
        $this->add(RouteAnnotation::class);
        $this->add(Service\ImageService::class);
        $this->add(Service\ImageRepository::class);
        $this->add(Controller\HomeController::class);
        $this->addMiddleware(LocaleResolver::class);
        $this->addMiddleware(AnnotationProcessor::class);
    }

    public function init() {
        $annotations = $this->get(AnnotationProcessor::class);
        $annotations->addNamespace('Dynart\\Press\\Controller');
        $annotations->add(RouteAnnotation::class);

        parent::init();

        $translation = $this->get(Translation::class);
        $translation->add('press', '~/translations');
    }
}