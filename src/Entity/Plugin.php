<?php

namespace Dynart\Press\Entity;

class Plugin extends NodeEntity {

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $name;

    /** @column { "type": "bool", "notNull": true, "default": false } */
    public $active;
}