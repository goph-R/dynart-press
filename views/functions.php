<?php

use Dynart\Micro\App;
use Dynart\Micro\Config;
use Dynart\Micro\Request;

function site_url(string $uri, bool $withMTime = true) {
    return url('/sites/'.App::instance()->get(Request::class)->get('dir').$uri, $withMTime);
}

function getthumb_url($uri) {
    return base_url().App::instance()->get(Config::class)->get('photos.getthumb_prefix').$uri;
}

