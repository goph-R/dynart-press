<?php

namespace Dynart\Press\Entity;

class User extends NodeEntity {

    const EVENT_BEFORE_SAVE = 'user:before_save';
    const EVENT_AFTER_SAVE = 'user:after_save';

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "bool", "notNull": true } */
    public $active;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $email;

    /** @column { "type": "string", "size": 255 } */
    public $password;
}