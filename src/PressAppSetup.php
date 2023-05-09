<?php

namespace Dynart\Press;

use Dynart\Micro\App;
use Dynart\Micro\Entities\QueryExecutor;
use Dynart\Micro\Translation;
use Dynart\Micro\Database;
use Dynart\Micro\Database\MariaDatabase;
use Dynart\Micro\Database\PdoBuilder;
use Dynart\Micro\Middleware\AnnotationProcessor;
use Dynart\Micro\Middleware\LocaleResolver;
use Dynart\Micro\Annotation\RouteAnnotation;

use Dynart\Micro\Entities\ColumnAnnotation;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\QueryBuilder;
use Dynart\Micro\Entities\QueryBuilder\MariaQueryBuilder;

use Dynart\Micro\View;
use Dynart\Press\Admin\Controller\DashboardController;
use Dynart\Press\Controller\HomeController;
use Dynart\Press\Entity\Db_Migration;
use Dynart\Press\Entity\Node;
use Dynart\Press\Entity\User;
use Dynart\Press\Entity\User_Role;
use Dynart\Press\Entity\User_Permission;
use Dynart\Press\Entity\Role;
use Dynart\Press\Entity\Role_Text;
use Dynart\Press\Entity\Role_Permission;
use Dynart\Press\Entity\Permission;
use Dynart\Press\Entity\Permission_Text;
use Dynart\Press\Entity\Plugin;
use Dynart\Press\Entity\Setting;

use Dynart\Press\Service\DbMigrationService;
use Dynart\Press\Service\DbMigrationSqlGenerator;
use Dynart\Press\Service\EventService;
use Dynart\Press\Service\MediaRepository;
use Dynart\Press\Service\MediaService;
use Dynart\Press\Service\NodeService;
use Dynart\Press\Service\DateService;
use Dynart\Press\Service\PluginRepository;
use Dynart\Press\Service\PluginService;
use Dynart\Press\Service\UserService;

class PressAppSetup {

    const NAMESPACE_ENTITY = "Dynart\\Press\\Entity";
    const NAMESPACE_ADMIN_CONTROLLER = 'Dynart\\Press\\Admin\\Controller';
    const NAMESPACE_CONTROLLER = 'Dynart\\Press\\Controller';

    public static function create(App $app, bool $isAdmin) {

        $app->add(PdoBuilder::class);
        $app->add(Database::class, MariaDatabase::class);
        $app->add(EntityManager::class);
        $app->add(QueryBuilder::class, MariaQueryBuilder::class);
        $app->add(QueryExecutor::class);
        $app->add(ColumnAnnotation::class);
        $app->add(Translation::class);
        $app->add(RouteAnnotation::class);

        $app->add(Db_Migration::class);
        $app->add(Node::class);
        $app->add(User::class);
        $app->add(User_Role::class);
        $app->add(User_Permission::class);
        $app->add(Role::class);
        $app->add(Role_Text::class);
        $app->add(Role_Permission::class);
        $app->add(Permission::class);
        $app->add(Permission_Text::class);
        $app->add(Plugin::class);
        $app->add(Setting::class);

        $app->add(DbMigrationService::class);
        $app->add(DateService::class);
        $app->add(EventService::class);
        $app->add(PluginRepository::class);
        $app->add(MediaService::class);
        $app->add(MediaRepository::class);
        $app->add(UserService::class);
        $app->add(NodeService::class);

        $app->addMiddleware(LocaleResolver::class);
        $app->addMiddleware(AnnotationProcessor::class);
        $app->addMiddleware(PluginService::class);

        $annotations = $app->get(AnnotationProcessor::class);
        $annotations->add(RouteAnnotation::class);
        $annotations->add(ColumnAnnotation::class);
        $annotations->addNamespace(self::NAMESPACE_ENTITY);

        if ($isAdmin) {
            $annotations->addNamespace(self::NAMESPACE_ADMIN_CONTROLLER);
            $app->add(DashboardController::class);
        } else {
            $annotations->addNamespace(self::NAMESPACE_CONTROLLER);
            $app->add(HomeController::class);
        }
    }

    public static function init(App $app, bool $isAdmin) {
        /** @var Translation $translation */
        $translation = $app->get(Translation::class);
        $view = $app->get(View::class);

        if ($isAdmin) {
            $translation->add('admin', '~/admin/translations');
            $view->addFolder('admin', '~/admin/views');
        } else {
            $translation->add('press', '~/translations');
        }

        $dbMigrationService = $app->get(DbMigrationService::class);
        $dbMigrationService->addFolder(self::NAMESPACE_ENTITY, '~/content/sql');
    }

    public static function initPlugins(App $app, bool $isAdmin) {
        $plugins = $app->get(PluginService::class);
        if ($isAdmin) {
            $plugins->adminInit();
        } else {
            $plugins->init();
        }

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
    }

}