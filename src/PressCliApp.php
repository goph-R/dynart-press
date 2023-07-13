<?php

namespace Dynart\Press;

use Dynart\Micro\AppException;
use Dynart\Micro\CliApp;
use Dynart\Micro\CliCommands;
use Dynart\Micro\CliOutput;
use Dynart\Micro\Config;
use Dynart\Micro\Request;
use Dynart\Micro\Router;
use Dynart\Micro\View;

use Dynart\Press\Service\DbMigrationService;
use Dynart\Press\Service\DbMigrationSqlGenerator;
use Dynart\Press\Service\PluginService;

class PressCliApp extends CliApp {

    /** @var DbMigrationService */
    private $dbMigrationService;

    public function __construct(array $configPaths) {
        parent::__construct($configPaths);
        $this->add(Router::class);
        $this->add(Request::class);
        $this->add(View::class);
        $this->add(DbMigrationSqlGenerator::class);
        PressAppSetup::create($this, $this->isAdmin());
    }

    public function init() {
        parent::init();
        PressAppSetup::init($this, $this->isAdmin());

        /** @var CliCommands $commands */
        $commands = $this->get(CliCommands::class);
        $commands->add('db:migration-sql', [$this, 'dbMigrationSql'], ['namespace'], ['create']);
        $commands->add('db:migrate', [$this, 'dbMigrate']);
        $commands->add('app:routes', [$this, 'appRoutes']);
        $commands->add('app:config', [$this, 'appConfig'], [], ['array']);

        $this->output = $this->get(CliOutput::class);
        $this->output->setUseColor($this->useColor());
        $this->dbMigrationService = $this->get(DbMigrationService::class);
    }

    public function process() {
        PressAppSetup::initPlugins($this, $this->isAdmin());
        /** @var PluginService $plugins */
        $plugins = $this->get(PluginService::class);
        $plugins->cliInit();
        try {
            parent::process();
        } catch (\Exception $e) {
            $this->outputException($e);
        }
    }

    public function dbMigrationSql(array $params) {
        $dbMigrationSqlGenerator = $this->get(DbMigrationSqlGenerator::class);
        $namespace = $this->paramValue($params, 'namespace');
        $message = $this->paramValue($params, 0);
        $create = $params['create'];
        $namespaces = $namespace ? [$namespace] : $this->dbMigrationService->namespaces();
        $newMigration = false;
        if (!$message) {
            throw new AppException("Provide a message for the migration!");
        }
        foreach ($namespaces as $n) {
            /** @var DbMigrationSqlGenerator $sql */
            $sql = $dbMigrationSqlGenerator->generate($n);
            if (!$sql) {
                continue;
            }
            $newMigration = true;
            $this->output->setColor(CliOutput::BLUE);
            $this->output->writeLine("Namespace: $n");
            $this->output->setColor(CliOutput::COLOR_OFF);
            if ($create) {
                $path = $this->dbMigrationService->newSqlPath($n, $message);
                if (file_put_contents($path, $sql) === false) {
                    throw new AppException("Couldn't create: ".$path);
                }
                $this->output->writeLine("Created: $path");
            } else {
                $this->output->writeLine($sql);
            }
        }
        if (!$newMigration) {
            $this->output->writeLine("No new migrations found.");
        }
    }

    public function dbMigrate() {
        $migratedSqlPaths = $this->dbMigrationService->migrate();
        if (empty($migratedSqlPaths)) {
            $this->output->writeLine("No new migrations found.");
        } else {
            $this->output->setColor(CliOutput::GREEN);
            $this->output->writeLine("Database migration was successful.");
            $this->output->setColor(CliOutput::WHITE);
            $this->output->writeLine("Migrated SQL files:");
            $this->output->setColor(CliOutput::COLOR_OFF);
            foreach ($migratedSqlPaths as $path) {
                $this->output->writeLine($path);
            }
        }
    }

    public function appRoutes() {
        /** @var Router $router */
        $router = $this->get(Router::class);
        foreach ($router->routes() as $method => $methodRoutes) {
            foreach ($methodRoutes as $route => $routeData) {
                $this->output->setColor(CliOutput::WHITE);
                $this->output->writeLine("$method $route");
                $this->output->setColor(CliOutput::DARK_GRAY);
                $this->output->writeLine("$routeData[0]::$routeData[1]\n");
            }
        }
    }

    public function appConfig(array $params) {
        $name = $this->paramValue($params, 0);
        if (!$name) {
            throw new AppException("Please provide a config name!");
        }
        $config = $this->get(Config::class);
        $array = $params['array'];
        if ($array) {
            foreach ($config->getArray($name, []) as $n => $v) {
                $this->output->writeLine("$n: $v");
            }
        } else {
            $this->output->writeLine("$name: ".$config->get($name));
        }
    }

    private function isAdmin() {
        return in_array('-admin', $_SERVER['argv']);
    }

    private function useColor() {
        return !in_array('-no-color', $_SERVER['argv']);
    }

    private function paramValue(array $params, $nameOrIndex, $default = null) {
        return array_key_exists($nameOrIndex, $params) ? $params[$nameOrIndex] : $default;
    }

    private function outputException(\Exception $e) {
        $this->output->setColor(CliOutput::RED);
        $this->output->writeLine($e->getMessage());
        $this->output->setColor(CliOutput::WHITE);
        $type = get_class($e);
        $this->output->writeLine("`$type` in {$e->getFile()} on line {$e->getLine()}");
        $this->output->setColor(CliOutput::DARK_GRAY);
        $this->output->writeLine($e->getTraceAsString());
        $this->finish(1);
    }
}