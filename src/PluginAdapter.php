<?php

namespace Dynart\Press;

class PluginAdapter implements PluginInterface {

    public function init() {}
    public function adminInit() {}
    public function cliInit() {}

    public function scripts() {
        return [];
    }

    public function adminScripts() {
        return [];
    }
}