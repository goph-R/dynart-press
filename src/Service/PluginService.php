<?php

namespace Dynart\Press\Service;

use Dynart\Micro\App;
use Dynart\Micro\Middleware;

use Dynart\Press\PluginInterface;

class PluginService implements Middleware {

    /** @var PluginRepository */
    private $repository;

    /** @var PluginInterface[] */
    private $activePlugins = [];

    public function __construct(PluginRepository $repository) {
        $this->repository = $repository;
    }

    public function run(): void {
        $app = App::instance();
        $names = $this->repository->findAllActiveNames();
        foreach ($names as $name) { // TODO: dependency?
            $class = "Dynart\\Press\\Plugin\\{$name}\\{$name}Plugin";
            $app->add($class);
            $this->activePlugins[] = $app->get($class);
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

}