<?php

namespace Dynart\Press\Entity;

use Dynart\Micro\Entities\Entity;

class User_Permission extends Entity {

    /** @column { "type": "int", "notNull": true, "primaryKey": true, "foreignKey": ["Dynart\\Press\\Entity\\User", "id"], "onDelete": "cascade" } */
    public $user_id;

    /** @column { "type": "int", "notNull": true, "primaryKey": true, "foreignKey": ["Dynart\\Press\\Entity\\Permission", "id"], "onDelete": "cascade" } */
    public $permission_id;
}