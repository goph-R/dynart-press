<?php

namespace Dynart\Press\Service;

use Dynart\Micro\Entities\Database;
use Dynart\Micro\Entities\Repository;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\QueryExecutor;

use Dynart\Press\Entity\Plugin;

class PluginRepository extends Repository {

    private $entityClassName = Plugin::class;

    /** @var EntityManager */
    private $entityManager;

    /** @var QueryExecutor */
    private $queryExecutor;

    public function __construct(Database $db, EntityManager $entityManager, QueryExecutor $queryExecutor) {
        parent::__construct($db);
        $this->entityManager = $entityManager;
        $this->queryExecutor = $queryExecutor;
    }

    public function allFields() {
        return array_keys($this->entityManager->tableColumns($this->entityClassName));
    }

    public function tableName() {
        return $this->entityManager->tableNameByClass($this->entityClassName);
    }

    public function findAllActiveNames() {
        if ($this->queryExecutor->isTableExists($this->entityClassName)) {
            return $this->db->fetchColumn("select {$this->db->escapeName('name')} from {$this->safeTableName()} where active = 1");
        }
        return [];
    }

}