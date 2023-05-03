<?php

namespace Dynart\Press;

interface PluginInterface {
    public function init();
    public function adminInit();
    public function cliInit();
}