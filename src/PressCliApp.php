<?php

namespace Dynart\Press;

use Dynart\Micro\CliApp;
use Dynart\Micro\CliCommands;
use Dynart\Micro\Middleware\AnnotationProcessor;
use Dynart\Micro\Request;
use Dynart\Micro\Router;

use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\QueryBuilder;

use Dynart\Press\Service\DbMigrationService;
use Dynart\Press\Service\PluginService;

use Dynart\Press\Admin\Controller\DashboardController;
use Dynart\Press\Controller\HomeController;

class PressCliApp extends CliApp {

    /** @var PressAppSetup */
    protected $appHelper;

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->appHelper = new PressAppSetup();
        $this->add(Router::class);
        $this->add(Request::class);
        $this->appHelper->create($this);

    }

    public function init() {
        parent::init();
        $this->appHelper->init($this);

        // TODO: add the controllers and namespaces for app:routes (how to do this properly?)
        $this->add(DashboardController::class);
        $this->add(HomeController::class);

        $annotations = $this->get(AnnotationProcessor::class);
        $annotations->addNamespace('Dynart\\Press\\Admin\\Controller');
        $annotations->addNamespace('Dynart\\Press\\Controller');
        //

        /** @var CliCommands $commands */
        $commands = $this->get(CliCommands::class);
        $commands->add('db:init-sql', [$this, 'dbInitSql']);
        $commands->add('db:migration-sql', [$this, 'dbMigrationSql']);
        $commands->add('db:migrate', [$this, 'dbMigrate']);
        $commands->add('app:routes', [$this, 'appRoutes']);
    }

    public function process() {
        /** @var PluginService $plugins */
        $plugins = $this->get(PluginService::class);
        $plugins->cliInit();

        parent::process();
    }

    public function dbInitSql() {
        /** @var EntityManager $entityManager */
        $entityManager = $this->get(EntityManager::class);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->get(QueryBuilder::class);

        $result = '';
        foreach ($entityManager->tableNames() as $className => $tableName) {
            $result .= $queryBuilder->createTable($className).";\n";
        }
        return $result;
    }

    public function dbMigrationSql() {
    }

    public function dbMigrate() {
        /** @var DbMigrationService $dbMigrationService */
        $dbMigrationService = $this->get(DbMigrationService::class);
        $dbMigrationService->migrate();
    }

    public function appRoutes() {
        /** @var Router $router */
        $router = $this->get(Router::class);
        foreach ($router->routes() as $method => $methodRoutes) {
            foreach ($methodRoutes as $route => $routeData) {
                echo "\n$method $route\n$routeData[0]::$routeData[1]\n";
            }
        }
    }

}