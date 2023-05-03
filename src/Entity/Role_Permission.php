<?php

namespace Dynart\Press\Entity;

use Dynart\Micro\Entities\Entity;

class Role_Permission extends Entity {

    /** @column { "type": "int", "notNull": true, "primaryKey": true, "foreignKey": ["Dynart\\Press\\Entity\\Role", "id"], "onDelete": "cascade" } */
    public $role_id;

    /** @column { "type": "int", "notNull": true, "primaryKey": true, "foreignKey": ["Dynart\\Press\\Entity\\Permission", "id"], "onDelete": "cascade" } */
    public $permission_id;
}