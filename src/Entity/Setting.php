<?php

namespace Dynart\Press\Entity;

class Setting extends NodeEntity {

    const EVENT_BEFORE_SAVE = 'setting:before_save';
    const EVENT_AFTER_SAVE = 'setting:after_save';

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $name;

    /** @column { "type": "string" } */
    public $value;
}