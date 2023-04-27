<?php

namespace Dynart\Press\Entity;

class Node {

    const EVENT_BEFORE_SAVE = 'node:before_save';
    const EVENT_AFTER_SAVE = 'node:after_save';

    /** @column { "type": "int", "notNull": true, "primaryKey": true, "autoIncrement": true } */
    public $id;

    /** @column { "type": "string", "size": 50, "fixSize": true, "notNull": true } */
    public $type;

    /** @column { "type": "int", "references": "user.id" } */
    public $created_by;

    /** @column { "type": "datetime", "default": "current_datetime" } */
    public $created_at;

    /** @column { "type": "int", "references": "user.id" } */
    public $updated_by;

    /** @column { "type": "datetime", "default": null } */
    public $updated_at;
}