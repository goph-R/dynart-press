<?php

namespace Dynart\Press\Service;

use Dynart\Micro\App;
use Dynart\Micro\Config;
use Dynart\Micro\Middleware;

use Dynart\Press\PluginInterface;

class PluginService implements Middleware {

    const NAMESPACE_PREFIX = "Dynart\\Press\\Plugin";

    /** @var Config */
    private $config;

    /** @var PluginRepository */
    private $repository;

    /** @var DbMigrationService */
    private $dbMigrationService;

    /** @var PluginInterface[] */
    private $activePlugins = [];

    /** @var string[] */
    private $scripts = [];

    /** @var string[] */
    private $adminScripts = [];

    public function __construct(Config $config, PluginRepository $repository, DbMigrationService $dbMigrationService) {
        $this->config = $config;
        $this->repository = $repository;
        $this->dbMigrationService = $dbMigrationService;
    }

    public function run(): void {
        $names = $this->repository->findAllActiveNames();
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

        $app = App::instance();
        $app->add($class);
        $this->activePlugins[] = $app->get($class);

        $dir = $this->config->getFullPath("~/content/plugins/$name/sql");
        if (file_exists($dir)) {
            $this->dbMigrationService->addFolder($namespace, $dir);
        }
    }
}