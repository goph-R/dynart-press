<?php

use Dynart\Micro\Micro;
use Dynart\Micro\Config;
use Dynart\Micro\Request;

function site_url(string $uri, bool $withMTime = true) {
    return url('/content/sites/'.Micro::get(Request::class)->get('dir').$uri, $withMTime);
}

function getthumb_url($uri) {
    return base_url().Micro::get(Config::class)->get('photos.getthumb_prefix').$uri;
}

