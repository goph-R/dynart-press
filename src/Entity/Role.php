<?php

namespace Dynart\Press\Entity;

class Role extends NodeEntity {

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "bool", "notNull": true, "default": true } */
    public $active;
}