<?php

namespace Dynart\Press\Service;

class DateService {
    public function now() {
        return gmdate('Y-m-d H:i:s');
    }
}