<?php

namespace Dynart\Press\Plugin\Test;

use Dynart\Micro\App;
use Dynart\Micro\CliCommands;
use Dynart\Micro\Micro;
use Dynart\Press\PluginAdapter;

/**
 * Plugin:
 *   init()
 *      $personList = Micro::get(PersonListService::class);
 *      $this->events->subscribe($personList->columnViewsCreatedEvent(), function(ColumnViews $columnViews) {
 *
 *      });
 *      $this->events->subscribe($personList->formCreatedEvent(), function(Form $form) {
 *
 *      });
 *      $this->events->subscribe($personList->queryCreatedEvent(), function(Form $form, Query $query) {
 *
 *      });
 */

class TestPlugin extends PluginAdapter {

    public function cliInit() {
        /** @var CliCommands $commands */
        $commands = Micro::get(CliCommands::class);
        $commands->add('test:arguments', [$this, 'testArguments'], ['param1', 'param2'], ['flag1', 'flag2']);
    }

    public function testArguments(array $params) {
        print_r($params);
    }
}