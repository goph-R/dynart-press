<?php

namespace Dynart\Press\Plugin\Test2;

use Dynart\Press\PluginInterface;

class Test2Plugin implements PluginInterface {

    public function init() {
        echo "Test2Plugin was initialized.";
    }

}