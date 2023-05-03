<?php

namespace Dynart\Press\Entity;

class Permission extends NodeEntity {

    const EVENT_BEFORE_SAVE = 'permission:before_save';
    const EVENT_AFTER_SAVE = 'permission:after_save';

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "bool", "notNull": true, "default": true } */
    public $active;
}