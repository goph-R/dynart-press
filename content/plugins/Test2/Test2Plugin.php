<?php

namespace Dynart\Press\Plugin\Test2;

use Dynart\Press\Plugin;

class Test2Plugin implements Plugin {

    public function init() {
        echo "Test2Plugin was initialized.";
    }

}