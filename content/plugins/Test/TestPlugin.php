<?php

namespace Dynart\Press\Plugin\Test;

use Dynart\Micro\App;
use Dynart\Micro\CliCommands;
use Dynart\Press\PluginAdapter;

class TestPlugin extends PluginAdapter {

    public function cliInit() {
        /** @var CliCommands $commands */
        $commands = App::instance()->get(CliCommands::class);
        $commands->add('test:arguments', [$this, 'testArguments'], ['param1', 'param2'], ['flag1', 'flag2']);
    }

    public function testArguments(array $params) {
        print_r($params);
    }
}