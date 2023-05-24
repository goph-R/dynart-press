<?php

namespace Dynart\Press\Service;

use Dynart\Micro\App;
use Dynart\Micro\AppException;
use Dynart\Micro\Config;
use Dynart\Micro\Database;
use Dynart\Micro\Entities\Entity;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\QueryBuilder;
use Dynart\Micro\Entities\QueryExecutor;

class DbMigrationSqlGenerator {

    /** @var Config */
    private $config;

    /** @var Database */
    private $db;

    /** @var EntityManager */
    private $entityManager;

    /** @var QueryBuilder */
    private $queryBuilder;

    /** @var QueryExecutor */
    private $queryExecutor;

    /** @var DateService */
    private $dateService;

    private $createdClassNames = [];
    private $tablePrefix;

    const PLACEHOLDER_TABLE_PREFIX = '!tablePrefix_';

    public function __construct(
        Config $config,
        Database $db,
        EntityManager $entityManager,
        QueryBuilder $queryBuilder,
        QueryExecutor $queryExecutor,
        DateService $dateService
    ) {
        $this->config = $config;
        $this->db = $db;
        $this->entityManager = $entityManager;
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
        $this->entityManager->setTableNamePrefix(self::PLACEHOLDER_TABLE_PREFIX);
        $newClassNames = [];
        foreach ($classNames as $className) {
            $classTableName = $this->tablePrefix.$this->entityManager->tableNameByClass($className, false);
            if (is_subclass_of($className, Entity::class)
                && !in_array($classTableName, $dbTableNames)) {
                $newClassNames[] = $className;
            }
        }
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
            throw new AppException("Circular entity dependency: ".join(' <- ', $dependencyStack));
        }
        $dependencyStack[] = $className;
        $result = '';
        $columns = $this->entityManager->tableColumns($className);
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
        $app = App::instance();
        $result = [];
        foreach ($app->interfaces() as $className) {
            if (substr($className, 0, strlen($namespace)) == $namespace) {
                $result[] = $className;
            }
        }
        return $result;
    }

    private function generateAlterTables($classNames): string {
        foreach ($classNames as $className) {
            $columns = $this->entityManager->tableColumns($className);
            $this->entityManager->setTableNamePrefix($this->tablePrefix);
            $dbColumns = $this->queryExecutor->findColumns($className);
            $this->entityManager->setTableNamePrefix(self::PLACEHOLDER_TABLE_PREFIX);
            
        }
        return '';
    }
}