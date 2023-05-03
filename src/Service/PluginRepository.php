<?php

namespace Dynart\Press\Service;

use Dynart\Micro\Database\Repository;

class PluginRepository extends Repository {

    protected $table = 'plugin';
    protected $allFields = [
        'id',
        'name',
        'active'
    ];

    public function findAllActiveNames() {
        return $this->db->fetchColumn("select `name` from !tableName where active = 1", [
            '!tableName' => $this->db->escapeName($this->db->configValue('table_prefix').$this->table)
        ]);
    }

}