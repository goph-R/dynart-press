<?php

namespace Dynart\Press\Service;

use Dynart\Micro\AppException;
use Dynart\Micro\Config;
use Dynart\Micro\Entities\Database;
use Dynart\Micro\Entities\EntityManager;
use Dynart\Micro\Entities\QueryExecutor;

use Dynart\Press\Entity\Db_Migration;
use Dynart\Press\StringUtil;

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

    public function __construct(
        Config $config,
        Database $db,
        EntityManager $entityManager,
        QueryExecutor $queryExecutor,
        DateService $dateService
    ) {
        $this->config = $config;
        $this->db = $db;
        $this->entityManager = $entityManager;
        $this->queryExecutor = $queryExecutor;
        $this->dateService = $dateService;
    }

    public function addFolder(string $namespace, string $path): void {
        $this->folders[$namespace] = $this->config->getFullPath($path);
    }

    public function migrate(): array {
        $this->queryExecutor->createTable(Db_Migration::class, true);
        $result = [];
        foreach ($this->folders as $namespace => $dir) {
            $result = array_merge($result, $this->migrateFolder($namespace, $dir));
        }
        return $result;
    }

    public function namespaces() {
        return array_keys($this->folders);
    }

    public function folderPath(string $namespace) {
        if (!array_key_exists($namespace, $this->folders)) {
            throw new AppException("Namespace doesn't exist: ".$namespace);
        }
        return $this->folders[$namespace];
    }

    public function newSqlPath(string $namespace, string $message): string {
        $folderPath = $this->folderPath($namespace);
        $date = gmdate('Y-m-d');
        $number = 0;
        do {
            $number++;
            $path = sprintf("$folderPath/{$date}_%03d", $number);
        }
        while (!empty(glob($path.'*.sql')));
        return $path.'_'.StringUtil::safeFilename($message).'.sql';

    }

    private function migrateFolder(string $namespace, string $dir): array {
        $sqlFiles = $this->findSqlFiles($dir);
        $dbMigrations = $this->findDbMigrations($namespace);
        $migratedNames = $this->checkExistingMigrations($dir, $dbMigrations, $sqlFiles);
        return $this->migrateNewSqlFiles($namespace, $sqlFiles, $migratedNames);
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

    private function migrateNewSqlFiles(string $namespace, array $sqlFiles, array $migratedNames): array {
        $allNames = array_keys($sqlFiles);
        $newNames = array_diff($allNames, $migratedNames);
        $result = [];
        if (empty($newNames)) {
            return $result;
        }
        sort($newNames);
        foreach ($newNames as $name) {
            $this->migrateSqlFile($namespace, $name, $sqlFiles[$name]);
            $result[] = $sqlFiles[$name]['path'];
        }
        return $result;
    }

    private function migrateSqlFile(string $namespace, string $name, array $sqlFile): void {
        $this->db->runInTransaction(function () use ($namespace, $name, $sqlFile) {
            $this->db->query(
                $sqlFile['content'],
                ['!tablePrefix_' => $this->db->configValue('table_prefix')]
            );
            $dbMigration = new Db_Migration();
            $dbMigration->name = $name;
            $dbMigration->namespace = $namespace;
            $dbMigration->hash = $sqlFile['hash'];
            $dbMigration->created_at = $this->dateService->now();
        });
    }
}