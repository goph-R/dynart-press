<?php

namespace Dynart\Press\Entity;

use Dynart\Micro\Entities\Entity;

class Node extends Entity {

    const EVENT_BEFORE_SAVE = 'node:before_save';
    const EVENT_AFTER_SAVE = 'node:after_save';

    /** @column { "type": "int", "notNull": true, "primaryKey": true, "autoIncrement": true } */
    public $id;

    /** @column { "type": "string", "size": 255, "fixSize": true, "notNull": true } */
    public $type;

    /** @column { "type": "int", "foreignKey": ["Dynart\\Press\\Entity\\User", "id"], "onDelete": "cascade" } */
    public $created_by;

    /** @column { "type": "datetime", "default": "now" } */
    public $created_at;

    /** @column { "type": "int", "foreignKey": ["Dynart\\Press\\Entity\\User", "id"], "onDelete": "cascade" } */
    public $updated_by;

    /** @column { "type": "datetime", "default": null } */
    public $updated_at;
}