<?php

namespace Dynart\Press;

use Dynart\Micro\CliApp;
use Dynart\Micro\CliCommands;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\QueryBuilder;
use Dynart\Micro\Request;
use Dynart\Micro\Router;
use Dynart\Press\Service\PluginService;

class PressCliApp extends CliApp {

    /** @var PressAppHelper */
    protected $appHelper;

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->appHelper = new PressAppHelper();
        $this->add(Router::class);
        $this->add(Request::class);
        $this->appHelper->create($this);
    }

    public function init() {
        parent::init();
        $this->appHelper->init($this);

        /** @var CliCommands $commands */
        $commands = $this->get(CliCommands::class);
        $commands->add('db:create-init-sql', [$this, 'createInitSql']);
        $commands->add('db:create-migration-sql', [$this, 'createMigrationSql']);
        $commands->add('db:migrate', [$this, 'migrate']);
    }

    public function process() {
        /** @var PluginService $plugins */
        $plugins = $this->get(PluginService::class);
        $plugins->cliInit();

        parent::process();
    }

    public function createInitSql() {
        /** @var EntityManager $em */
        $em = $this->get(EntityManager::class);

        /** @var QueryBuilder $qb */
        $qb = $this->get(QueryBuilder::class);

        $result = '';
        foreach ($em->tableNames() as $className => $tableName) {
            $result .= $qb->createTable($className).";\n";
        }
        return $result;
    }

    public function createMigrationSql() {
    }

    public function migrate() {
    }

}