<?php

namespace Dynart\Press\Entity;

class Permission extends NodeEntity {

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "bool", "notNull": true, "default": true } */
    public $active;
}