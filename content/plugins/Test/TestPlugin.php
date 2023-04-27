<?php

namespace Dynart\Press\Plugin\Test;

use Dynart\Press\PluginInterface;

class TestPlugin implements PluginInterface {

    public function init() {
        echo "TestPlugin was initialized.";
    }

}