<?php

namespace Dynart\Press\Admin;

use Dynart\Micro\Middleware\AnnotationProcessor;
use Dynart\Micro\Translation;
use Dynart\Micro\View;
use Dynart\Micro\WebApp;

use Dynart\Press\PressAppSetup;
use Dynart\Press\Service\PluginService;

use Dynart\Press\Admin\Controller\DashboardController;

class AdminApp extends WebApp {

    /** @var PressAppSetup */
    private $appSetup;

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->appSetup = new PressAppSetup();
        $this->appSetup->create($this);

        $this->add(DashboardController::class);

        $annotations = $this->get(AnnotationProcessor::class);
        $annotations->addNamespace('Dynart\\Press\\Admin\\Controller');
    }

    public function init() {
        parent::init();
        $this->appSetup->init($this);

        $translation = $this->get(Translation::class);
        $translation->add('admin', '~/admin/translations');

        $view = $this->get(View::class);
        $view->addFolder('admin', '~/admin/views');

    }

    public function process() {
        /** @var PluginService $plugins */
        $plugins = $this->get(PluginService::class);
        $plugins->adminInit();

        parent::process();
    }
}