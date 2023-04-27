<?php

namespace Dynart\Press\Entity;

use Dynart\Micro\Entities\Entity;

class Node extends Entity {

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