<?php

namespace Dynart\Press\Service;

use Dynart\Micro\Database\Repository;

class MediaRepository extends Repository {

    protected $table = 'image';
    protected $allFields = [
        'id',
        'width',
        'height',
        'created_at',
        'updated_at',
        'path',
        'path_updated_at',
        'title'
    ];

    protected function getWhere(array $params) {
        $result = '';
        if (isset($params['dir'])) {
            $result = 'WHERE dir = :dir';
            $this->sqlParams[':dir'] = $params['dir'];
        }
        return $result;
    }
}