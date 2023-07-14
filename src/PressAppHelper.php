<?php

namespace Dynart\Press;

use Dynart\Micro\App;
use Dynart\Micro\Micro;
use Dynart\Micro\Translation;
use Dynart\Micro\Middleware\AnnotationProcessor;
use Dynart\Micro\Middleware\LocaleResolver;
use Dynart\Micro\Annotation\RouteAnnotation;

use Dynart\Micro\Entities\Database;
use Dynart\Micro\Entities\Database\MariaDatabase;
use Dynart\Micro\Entities\Database\PdoBuilder;
use Dynart\Micro\Entities\ColumnAnnotation;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\QueryBuilder;
use Dynart\Micro\Entities\QueryBuilder\MariaQueryBuilder;
use Dynart\Micro\Entities\QueryExecutor;

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
use Dynart\Press\Service\EventService;
use Dynart\Press\Service\MediaRepository;
use Dynart\Press\Service\MediaService;
use Dynart\Press\Service\NodeService;
use Dynart\Press\Service\DateService;
use Dynart\Press\Service\PluginRepository;
use Dynart\Press\Service\PluginService;
use Dynart\Press\Service\UserService;

class PressAppHelper {

    const NAMESPACE_ENTITY = "Dynart\\Press\\Entity";
    const NAMESPACE_ADMIN_CONTROLLER = 'Dynart\\Press\\Admin\\Controller';
    const NAMESPACE_CONTROLLER = 'Dynart\\Press\\Controller';

    public static function create(App $app, bool $isAdmin) {

        Micro::add(PdoBuilder::class);
        Micro::add(Database::class, MariaDatabase::class);
        Micro::add(EntityManager::class);
        Micro::add(QueryBuilder::class, MariaQueryBuilder::class);
        Micro::add(QueryExecutor::class);
        Micro::add(ColumnAnnotation::class);
        Micro::add(Translation::class);
        Micro::add(RouteAnnotation::class);

        Micro::add(Db_Migration::class);
        Micro::add(Node::class);
        Micro::add(User::class);
        Micro::add(User_Role::class);
        Micro::add(User_Permission::class);
        Micro::add(Role::class);
        Micro::add(Role_Text::class);
        Micro::add(Role_Permission::class);
        Micro::add(Permission::class);
        Micro::add(Permission_Text::class);
        Micro::add(Plugin::class);
        Micro::add(Setting::class);

        Micro::add(DbMigrationService::class);
        Micro::add(DateService::class);
        Micro::add(EventService::class);
        Micro::add(PluginRepository::class);
        Micro::add(MediaService::class);
        Micro::add(MediaRepository::class);
        Micro::add(UserService::class);
        Micro::add(NodeService::class);

        $app->addMiddleware(LocaleResolver::class);
        $app->addMiddleware(AnnotationProcessor::class);
        $app->addMiddleware(PluginService::class);

        $annotations = Micro::get(AnnotationProcessor::class);
        $annotations->add(RouteAnnotation::class);
        $annotations->add(ColumnAnnotation::class);
        $annotations->addNamespace(self::NAMESPACE_ENTITY);

        if ($isAdmin) {
            $annotations->addNamespace(self::NAMESPACE_ADMIN_CONTROLLER);
            Micro::add(DashboardController::class);
        } else {
            $annotations->addNamespace(self::NAMESPACE_CONTROLLER);
            Micro::add(HomeController::class);
        }
    }

    public static function init(bool $isAdmin) {
        /** @var Translation $translation */
        $translation = Micro::get(Translation::class);
        $view = Micro::get(View::class);

        if ($isAdmin) {
            $translation->add('admin', '~/admin/translations');
            $view->addFolder('admin', '~/admin/views');
        } else {
            $translation->add('press', '~/translations');
        }

        $dbMigrationService = Micro::get(DbMigrationService::class);
        $dbMigrationService->addFolder(self::NAMESPACE_ENTITY, '~/content/sql');
    }

    public static function initPlugins(bool $isAdmin) {
        $pluginService = Micro::get(PluginService::class);
        if ($isAdmin) {
            $pluginService->adminInit();
        } else {
            $pluginService->init();
        }

        /*
        $eventService = Micro::get(EventService::class);
        $eventService->subscribe(User::EVENT_BEFORE_SAVE, function(User $user) {
            var_dump($user);
        });

        $db = Micro::get(Database::class);
        $nodeService = Micro::get(NodeService::class);
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