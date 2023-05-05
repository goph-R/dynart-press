<?php

namespace Dynart\Press\Service;

use Dynart\Micro\AppException;
use Dynart\Micro\Config;
use Dynart\Micro\Database;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\QueryExecutor;

use Dynart\Press\Entity\Db_Migration;

class DbMigrationService {

    /** @var Config */
    private $config;

    /** @var Database */
    private $db;

    /** @var EntityManager */
    private $entityManager;

    /** @var QueryExecutor */
    private $queryExecutor;

    /** @var DateService */
    private $dateService;

    private $folders = [];

    public function __construct(Config $config, Database $db, EntityManager $em, QueryExecutor $qe, DateService $dateService) {
        $this->config = $config;
        $this->db = $db;
        $this->entityManager = $em;
        $this->queryExecutor = $qe;
        $this->dateService = $dateService;
    }

    public function addFolder(string $namespace, string $path): void {
        $this->folders[$namespace] = $this->config->getFullPath($path);
    }

    public function migrate(): void {
        $this->queryExecutor->createTable(Db_Migration::class, true);
        foreach ($this->folders as $namespace => $dir) {
            $this->migrateFolder($namespace, $dir);
        }
    }

    private function migrateFolder(string $namespace, string $dir): void {
        $sqlFiles = $this->findSqlFiles($dir);
        $dbMigrations = $this->findDbMigrations($namespace);
        $migratedNames = $this->checkExistingMigrations($dir, $dbMigrations, $sqlFiles);
        $this->migrateNewSqlFiles($namespace, $sqlFiles, $migratedNames);
    }

    private function findSqlFiles(string $dir) {
        $sqlFiles = [];
        $d = dir($dir);
        while (false !== ($entry = $d->read())) {
            $path = $dir.'/'.$entry;
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($ext == 'sql') {
                $content = file_get_contents($path);
                $sqlFiles[pathinfo($path, PATHINFO_FILENAME)] = [
                    'path' => $path,
                    'hash' => md5($content),
                    'content' => $content
                ];
            }
        }
        return $sqlFiles;
    }

    private function findDbMigrations(string $namespace): array {
        $safeTableName = $this->entityManager->safeTableName(Db_Migration::class);
        $safeNamespaceName = $this->db->escapeName('namespace');
        return $this->db->fetchAll(
            "select * from $safeTableName where $safeNamespaceName = :namespace",
            [':namespace' => $namespace],
            Db_Migration::class
        );
    }

    private function checkExistingMigrations(string $dir, array $dbMigrations, array $sqlFiles): array {
        $migratedNames = [];
        /** @var Db_Migration $dbMigration */
        foreach ($dbMigrations as $dbMigration) {
            if (!array_key_exists($dbMigration->name, $sqlFiles)) {
                throw new AppException("Missing SQL migration file '$dir/{$dbMigration->name}.sql'");
            }
            if ($dbMigration->hash != $sqlFiles[$dbMigration->name]['hash']) {
                throw new AppException("Hash does not match in '$dir/{$dbMigration->name}.sql'");
            }
            $migratedNames[] = $dbMigration->name;
        }
        return $migratedNames;
    }

    private function migrateNewSqlFiles(string $namespace, array $sqlFiles, array $migratedNames): void {
        $allNames = array_keys($sqlFiles);
        $newNames = array_diff($allNames, $migratedNames);
        sort($newNames);
        foreach ($newNames as $name) {
            $this->migrateSqlFile($namespace, $name, $sqlFiles[$name]);
        }
    }

    private function migrateSqlFile(string $namespace, string $name, array $sqlFile): void {
        $this->db->runInTransaction(function () use ($namespace, $name, $sqlFile) {
            $this->db->query($sqlFile['content']);
            $dbMigration = new Db_Migration();
            $dbMigration->name = $name;
            $dbMigration->namespace = $namespace;
            $dbMigration->hash = $sqlFile['hash'];
            $dbMigration->created_at = $this->dateService->now();
            $this->entityManager->save($dbMigration);
        });
    }

}