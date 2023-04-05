<?php

namespace Dynart\Press;

use Dynart\Micro\I18n\LocaleResolver;
use Dynart\Micro\I18n\Translation;
use Dynart\Micro\WebApp;

require_once 'views/functions.php';

class PressApp extends WebApp {

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->add(Translation::class);
        $this->add(LocaleResolver::class);
        $this->add(ImageService::class);
        $this->add(ImageRepository::class);
        $this->add(HomeController::class);
        $this->addMiddleware(LocaleResolver::class);
    }

    public function init() {
        parent::init();
        $translation = $this->get(Translation::class);
        $translation->add('press', '/translations');
    }
}