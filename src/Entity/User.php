<?php

namespace Dynart\Press\Entity;

use Dynart\Press\NodeType;

class User extends NodeType {

    const EVENT_BEFORE_SAVE = 'user:before_save';
    const EVENT_AFTER_SAVE = 'user:after_save';

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "bool", "notNull": true } */
    public $deleted;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $email;

    /** @column { "type": "string", "size": 255 } */
    public $password;

    public function beforeSave(bool $isNew): void {
        parent::beforeSave($isNew);
        $this->events->emit(self::EVENT_BEFORE_SAVE, [$this, $isNew]);
    }

    public function afterSave(bool $isNew): void {
        $this->events->emit(self::EVENT_AFTER_SAVE, [$this, $isNew]);
    }
}