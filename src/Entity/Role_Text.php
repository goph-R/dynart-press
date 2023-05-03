<?php

namespace Dynart\Press\Entity;

use Dynart\Micro\Entities\Entity;

class Role_Text extends Entity {

    /** @column { "type": "int", "notNull": true, "primaryKey": true, "foreignKey": ["Dynart\\Press\\Entity\\Role", "id"], "onDelete": "cascade" } */
    public $text_id;

    /** @column { "type": "string", "notNull": true, "size": 7, "primaryKey": true } */
    public $locale;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $name;
}