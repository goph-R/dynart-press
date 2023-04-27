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

use Dynart\Micro\Entities\ColumnAnnotation;
use Dynart\Micro\Entities\EntityManager;

use Dynart\Press\Entity\Node;
use Dynart\Press\Entity\User;
use Dynart\Press\Entity\Plugin;

use Dynart\Press\Service\EventService;
use Dynart\Press\Service\MediaRepository;
use Dynart\Press\Service\MediaService;
use Dynart\Press\Service\NodeService;
use Dynart\Press\Service\NowProvider;
use Dynart\Press\Service\PluginService;
use Dynart\Press\Service\PluginRepository;
use Dynart\Press\Service\UserService;

use Dynart\Press\Controller\HomeController;

class PressApp extends WebApp {

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);

        $this->add(PdoBuilder::class);
        $this->add(Database::class, MariaDatabase::class);
        $this->add(Translation::class);

        // entities
        $this->add(Node::class);
        $this->add(User::class);
        $this->add(Plugin::class);

        // middlewares
        $this->addMiddleware(LocaleResolver::class);
        $this->addMiddleware(AnnotationProcessor::class);
        $this->addMiddleware(PluginService::class);

        // services
        $this->add(NowProvider::class);
        $this->add(EntityManager::class);
        $this->add(EventService::class);
        $this->add(PluginRepository::class);
        $this->add(MediaService::class);
        $this->add(MediaRepository::class);
        $this->add(UserService::class);
        $this->add(NodeService::class);

        // controllers
        $this->add(HomeController::class);

        // annotations
        $this->add(RouteAnnotation::class);
        $this->add(ColumnAnnotation::class);

        /** @var AnnotationProcessor $annotations */
        $annotations = $this->get(AnnotationProcessor::class);
        $annotations->addNamespace('Dynart\\Press\\Controller');
        $annotations->addNamespace('Dynart\\Press\\Entity');
        $annotations->add(RouteAnnotation::class);
        $annotations->add(ColumnAnnotation::class);
    }

    public function init() {
        parent::init();
        try {
            /** @var Translation $translation */
            $translation = $this->get(Translation::class);
            $translation->add('press', '~/translations');

            /*
            $db = $this->get(Database::class);
            $nodeService = $this->get(NodeService::class);

            $db->runInTransaction(function () use ($nodeService) {
                $plugin = new Plugin();
                $plugin->active = 1;
                $plugin->name = 'Test';
                $nodeService->save($plugin);
            });


            $db->runInTransaction(function () use ($nodeService) {
                $user = new User();
                $user->active = 1;
                $user->email = 'hejejehoj@gmail.com';
                $user->password = 'password';
                $nodeService->save($user);
            });
            */

            /** @var PluginService $plugins */
            $plugins = $this->get(PluginService::class);
            $plugins->init();
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }
}