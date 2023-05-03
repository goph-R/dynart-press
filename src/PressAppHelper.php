<?php

namespace Dynart\Press;

use Dynart\Micro\App;
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

use Dynart\Press\Service\EventService;
use Dynart\Press\Service\MediaRepository;
use Dynart\Press\Service\MediaService;
use Dynart\Press\Service\NodeService;
use Dynart\Press\Service\NowProvider;
use Dynart\Press\Service\PluginRepository;
use Dynart\Press\Service\PluginService;
use Dynart\Press\Service\UserService;

class PressAppHelper {

    /** @var AnnotationProcessor */
    private $annotations;

    public function create(App $app) {

        $app->add(PdoBuilder::class);
        $app->add(Database::class, MariaDatabase::class);
        $app->add(EntityManager::class);
        $app->add(QueryBuilder::class, MariaQueryBuilder::class);
        $app->add(ColumnAnnotation::class);

        $app->add(RouteAnnotation::class);

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

        $app->add(Translation::class);
        $app->add(NowProvider::class);
        $app->add(EventService::class);
        $app->add(PluginRepository::class);
        $app->add(MediaService::class);
        $app->add(MediaRepository::class);
        $app->add(UserService::class);
        $app->add(NodeService::class);

        $app->addMiddleware(LocaleResolver::class);
        $app->addMiddleware(AnnotationProcessor::class);
        $app->addMiddleware(PluginService::class);

        $this->annotations = $app->get(AnnotationProcessor::class);
        $this->annotations->add(RouteAnnotation::class);
        $this->annotations->add(ColumnAnnotation::class);
        $this->annotations->addNamespace('Dynart\\Press\\Entity');
    }

    public function init(App $app) {
        /** @var Translation $translation */
        $translation = $app->get(Translation::class);
        $translation->add('press', '~/translations');
    }

}