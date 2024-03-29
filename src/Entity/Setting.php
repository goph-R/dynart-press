<?php

namespace Dynart\Press\Entity;

class Setting extends NodeEntity {

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $name;

    /** @column { "type": "string" } */
    public $value;
}