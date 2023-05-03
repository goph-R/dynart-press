<?php

namespace Dynart\Press\Entity;

class Role extends NodeEntity {

    const EVENT_BEFORE_SAVE = 'role:before_save';
    const EVENT_AFTER_SAVE = 'role:after_save';

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "bool", "notNull": true, "default": true } */
    public $active;
}