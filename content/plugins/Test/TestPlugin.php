<?php

namespace Dynart\Press\Plugin\Test;

use Dynart\Press\Plugin;

class TestPlugin implements Plugin {

    public function init() {
        echo "TestPlugin was initialized.";
    }

}