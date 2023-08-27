<?php

namespace Dynart\Press\Entity;

use Dynart\Micro\Entities\Entity;

class Image extends Entity {

    /** @column { "type": "int", "notNull": true, "primaryKey": true } */
    public $id;

    /** @column { "type": "int", "notNull": true, "default": 0 } */
    public $width;

    /** @column { "type": "int", "notNull": true, "default": 0 } */
    public $height;

    /** @column { "type": "datetime", "notNull": true, "default": "now"} */
    public $created_at;

    /** @column { "type": "datetime", "default": null} */
    public $updated_at;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $dir;

    /** @column { "type": "string", "size": 255, "notNull": true } */
    public $path;

    /** @column { "type": "datetime", "default": null} */
    public $path_updated_at;

    /** @column { "type": "string", "size": 255, "notNull": true, "default": "" } */
    public $title;
}