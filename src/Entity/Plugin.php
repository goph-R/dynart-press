<?php

namespace Dynart\Press\Entity;

use Dynart\Press\NodeType;

class Plugin extends NodeType {

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $name;

    /** @column { "type": "bool", "notNull": true } */
    public $active;
}