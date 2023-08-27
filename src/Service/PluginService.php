<?php

namespace Dynart\Press\Service;

use Dynart\Micro\App;
use Dynart\Micro\Config;
use Dynart\Micro\Entities\Query;
use Dynart\Micro\Entities\QueryExecutor;
use Dynart\Micro\Micro;
use Dynart\Micro\Middleware;

use Dynart\Press\Entity\Plugin;
use Dynart\Press\PluginInterface;

class PluginService implements Middleware {

    const NAMESPACE_PREFIX = "Dynart\\Press\\Plugin";

    /** @var Config */
    private $config;

    /** @var QueryExecutor */
    private $queryExecutor;

    /** @var DbMigrationService */
    private $dbMigrationService;

    /** @var PluginInterface[] */
    private $activePlugins = [];

    /** @var string[] */
    private $scripts = [];

    /** @var string[] */
    private $adminScripts = [];

    public function __construct(Config $config, QueryExecutor $qe, DbMigrationService $dbMigrationService) {
        $this->config = $config;
        $this->queryExecutor = $qe;
        $this->dbMigrationService = $dbMigrationService;
    }

    public function findAllActiveNames() {
        if ($this->queryExecutor->isTableExist(Plugin::class)) {
            $query = new Query(Plugin::class);
            $query->addCondition('active = 1');
            return $this->queryExecutor->findAllColumn($query, 'name');
        }
        return [];
    }

    public function run(): void {
        $names = $this->findAllActiveNames();
        foreach ($names as $name) { // TODO: dependency?
            $this->add($name);
        }
    }

    public function init(): void {
        foreach ($this->activePlugins as $plugin) {
            $plugin->init();
            $this->scripts = array_merge($this->scripts, $plugin->scripts());
            $this->adminScripts = array_merge($this->adminScripts, $plugin->adminScripts());
        }
    }

    public function adminInit(): void {
        foreach ($this->activePlugins as $plugin) {
            $plugin->adminInit();
        }
    }

    public function cliInit(): void {
        foreach ($this->activePlugins as $plugin) {
            $plugin->cliInit();
        }
    }

    private function add(string $name): void {
        $namespace = self::NAMESPACE_PREFIX . "\\$name";
        $class = "$namespace\\{$name}Plugin";
        Micro::add($class);
        $this->activePlugins[] = Micro::get($class);
        $dir = $this->config->getFullPath("~/content/plugins/$name/sql");
        if (file_exists($dir)) {
            $this->dbMigrationService->addFolder($namespace, $dir);
        }
    }
}