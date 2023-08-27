<?php

namespace Dynart\Press\Entity;

class User_Setting extends NodeEntity {

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "int", "notNull": true, "primaryKey": true, "foreignKey": ["Dynart\\Press\\Entity\\User", "id"], "onDelete": "cascade" } */
    public $user_id;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $name;

    /** @column { "type": "string" } */
    public $value;
}