<?php

namespace Dynart\Press\Service;

use Dynart\Micro\Database;
use Dynart\Micro\Database\Repository;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Press\Entity\Plugin;

class PluginRepository extends Repository {

    protected $entityClassName = Plugin::class;

    /** @var EntityManager */
    protected $entityManager;

    public function __construct(Database $db, EntityManager $entityManager) {
        parent::__construct($db);
        $this->entityManager = $entityManager;
    }

    public function allFields() {
        return array_keys($this->entityManager->tableColumns($this->entityClassName));
    }

    public function tableName() {
        return $this->entityManager->tableNameByClass($this->entityClassName);
    }

    public function findAllActiveNames() {
        return $this->db->fetchColumn("select {$this->db->escapeName('name')} from {$this->safeTableName()} where active = 1");
    }

}