<?php

namespace Dynart\Press\Entity;

use Dynart\Micro\Entities\Entity;

class Db_Migration extends Entity {

    /** @column { "type": "string", "size": 32, "fixSize": true, "notNull": true, "primaryKey": true } */
    public $name;

    /** @column { "type": "string", "size": 32, "fixSize": true, "notNull": true, "primaryKey": true } */
    public $namespace;

    /** @column { "type": "string", "size": 32, "fixSize": true, "notNull": true } */
    public $hash;

    /** @column { "type": "datetime", "default": "now" } */
    public $created_at;
}