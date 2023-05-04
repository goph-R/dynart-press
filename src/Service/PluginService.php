<?php

namespace Dynart\Press\Service;

use Dynart\Micro\App;
use Dynart\Micro\Config;
use Dynart\Micro\Middleware;

use Dynart\Press\PluginInterface;

class PluginService implements Middleware {

    /** @var Config */
    private $config;

    /** @var PluginRepository */
    private $repository;

    /** @var DbMigrationService */
    private $dbMigrationService;

    /** @var PluginInterface[] */
    private $activePlugins = [];

    public function __construct(Config $config, PluginRepository $repository, DbMigrationService $dbMigrationService) {
        $this->config = $config;
        $this->repository = $repository;
        $this->dbMigrationService = $dbMigrationService;
    }

    public function run(): void {
        $app = App::instance();
        $names = $this->repository->findAllActiveNames();
        foreach ($names as $name) { // TODO: dependency?
            $class = "Dynart\\Press\\Plugin\\{$name}\\{$name}Plugin";
            $app->add($class);
            $this->activePlugins[] = $app->get($class);
            $dir = $this->config->getFullPath("~/content/plugins/$name/sql");
            if (file_exists($dir)) {
                $this->dbMigrationService->addFolder(strtolower($name), $dir);
            }
        }
    }

    public function init(): void {
        foreach ($this->activePlugins as $plugin) {
            $plugin->init();
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

}