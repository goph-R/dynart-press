<?php

namespace Dynart\Press;

use Dynart\Micro\Middleware\AnnotationProcessor;
use Dynart\Micro\WebApp;

use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\QueryBuilder;

use Dynart\Press\Controller\HomeController;
use Dynart\Press\Service\PluginService;

class PressApp extends WebApp {

    /** @var PressAppHelper */
    private $appHelper;

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->appHelper = new PressAppHelper();
        $this->appHelper->create($this);

        $this->add(HomeController::class);

        $annotations = $this->get(AnnotationProcessor::class);
        $annotations->addNamespace('Dynart\\Press\\Controller');
   }

    public function init() {
        parent::init();
        $this->appHelper->init($this);

        /** @var PluginService $plugins */
        $plugins = $this->get(PluginService::class);
        $plugins->init();
    }

    public function process() {


        /*
        $eventService = $this->get(EventService::class);
        $eventService->subscribe(User::EVENT_BEFORE_SAVE, function(User $user) {
            var_dump($user);
        });

        $db = $this->get(Database::class);
        $nodeService = $this->get(NodeService::class);
        $db->runInTransaction(function () use ($nodeService) {
            $user = new User();
            $user->active = 1;
            $user->email = 'hejejehoj@gmail.com';
            $user->password = 'password';
            $nodeService->save($user);
        });

        $db->runInTransaction(function () use ($nodeService) {
            $plugin = new Plugin();
            $plugin->active = 1;
            $plugin->name = 'Test';
            $nodeService->save($plugin);
        });
        */

        parent::process();
    }
}