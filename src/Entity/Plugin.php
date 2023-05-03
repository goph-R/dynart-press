<?php

namespace Dynart\Press\Entity;

class Plugin extends NodeEntity {

    const EVENT_BEFORE_SAVE = 'plugin:before_save';
    const EVENT_AFTER_SAVE = 'plugin:after_save';

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $name;

    /** @column { "type": "bool", "notNull": true, "default": false } */
    public $active;
}