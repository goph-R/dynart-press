<?php

namespace Dynart\Press\Service;

use Dynart\Micro\App;
use Dynart\Micro\AppException;
use Dynart\Micro\Config;
use Dynart\Micro\Entities\Database;
use Dynart\Micro\Entities\Entity;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\QueryBuilder;
use Dynart\Micro\Entities\QueryExecutor;
use Dynart\Micro\Micro;
use Dynart\Micro\MicroException;

class DbMigrationSqlGenerator {

    /** @var Config */
    private $config;

    /** @var Database */
    private $db;

    /** @var EntityManager */
    private $em;

    /** @var QueryBuilder */
    private $queryBuilder;

    /** @var QueryExecutor */
    private $queryExecutor;

    /** @var DateService */
    private $dateService;

    private $createdClassNames = [];
    private $tablePrefix;

    public function __construct(
        Config $config,
        Database $db,
        EntityManager $em,
        QueryBuilder $queryBuilder,
        QueryExecutor $queryExecutor,
        DateService $dateService
    ) {
        $this->config = $config;
        $this->db = $db;
        $this->em = $em;
        $this->queryBuilder = $queryBuilder;
        $this->queryExecutor = $queryExecutor;
        $this->dateService = $dateService;
        $this->tablePrefix = $this->db->configValue('table_prefix');
    }

    public function generate(string $namespace): string {
        $result = '';

        $classNames = $this->findClassNames($namespace);
        $dbTableNames = $this->queryExecutor->listTables();

        $result = $this->generateCreateTables($classNames, $dbTableNames, $result);
        $result .= $this->generateAlterTables($classNames);

        return $result;
    }

    private function generateCreateTables($classNames, $dbTableNames, $result): string {
        $newClassNames = [];
        foreach ($classNames as $className) {
            $classTableName = $this->tablePrefix.$this->em->tableNameByClass($className, false);
            if (is_subclass_of($className, Entity::class)
                && !in_array($classTableName, $dbTableNames)) {
                $newClassNames[] = $className;
            }
        }
        $this->em->setUseEntityHashName(true);
        $this->createdClassNames = ['Dynart\\Press\\Entity\\Db_Migration'];
        foreach ($newClassNames as $className) {
            $result .= $this->generateCreateTable($newClassNames, $className);
        }
        return $result;
    }

    private function generateCreateTable(array $newClassNames, string $className, array $dependencyStack = []) {
        if (in_array($className, $this->createdClassNames)) {
            return '';
        }
        if (in_array($className, $dependencyStack)) {
            throw new MicroException("Circular entity dependency: ".join(' <- ', $dependencyStack));
        }
        $dependencyStack[] = $className;
        $result = '';
        $columns = $this->em->tableColumns($className);
        foreach ($columns as $column) {
            if (array_key_exists(EntityManager::COLUMN_FOREIGN_KEY, $column)) {
                $foreignKey = $column[EntityManager::COLUMN_FOREIGN_KEY];
                if (is_array($foreignKey) && isset($foreignKey[0]) && in_array($foreignKey[0], $newClassNames)) {
                    $result .= $this->generateCreateTable($newClassNames, $foreignKey[0], $dependencyStack);
                }
            }
        }
        $result .= $this->queryBuilder->createTable($className).";\n";
        $this->createdClassNames[] = $className;
        return $result;
    }

    private function findClassNames(string $namespace): array {
        /** @var App $app */
        $result = [];
        foreach (Micro::interfaces() as $className) {
            if (substr($className, 0, strlen($namespace)) == $namespace) {
                $result[] = $className;
            }
        }
        return $result;
    }

    private function generateAlterTables($classNames): string {
        return '';
    }
}