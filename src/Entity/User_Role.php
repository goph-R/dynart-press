<?php

namespace Dynart\Press\Entity;

use Dynart\Micro\Entities\Entity;

class User_Role extends Entity {

    /** @column { "type": "int", "notNull": true, "primaryKey": true, "foreignKey": ["Dynart\\Press\\Entity\\User", "id"], "onDelete": "cascade" } */
    public $user_id;

    /** @column { "type": "int", "notNull": true, "primaryKey": true, "foreignKey": ["Dynart\\Press\\Entity\\Role", "id"], "onDelete": "cascade" } */
    public $role_id;
}