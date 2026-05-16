<?php

declare(strict_types=1);

namespace App\Database;

use DateTimeImmutable;
use PDO;
use RuntimeException;

final class Migrator
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly string $migrationsPath,
    ) {
    }

    /**
     * Выполняет SQL-миграции
     */
    public function runMigrations(): void
    {
        $this->ensureMigrationsTable();

        foreach ($this->getPendingMigrations() as $migrationFile) {
            $this->runMigration($migrationFile);
        }
    }

    /**
     * Создает таблицу migrations (если нету)
     */
    private function ensureMigrationsTable(): void
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS migrations (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                migration VARCHAR(255) NOT NULL,
                executed_at DATETIME NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uk_migrations_migration (migration)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
        );
    }

    /**
     * Возвращает список файлов невыполненных миграций
     */
    private function getPendingMigrations(): array
    {
        $files = glob($this->migrationsPath . '/*.sql');

        if ($files === false) {
            throw new RuntimeException(sprintf('Unable to read migrations from "%s".', $this->migrationsPath));
        }

        sort($files, SORT_STRING);

        $pending = [];

        foreach ($files as $file) {
            $migration = basename($file);

            if (!$this->isExecuted($migration)) {
                $pending[] = $file;
            }
        }

        return $pending;
    }

    /**
     * Проверяет была ли миграция уже выполнена
     */
    private function isExecuted(string $migration): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT 1 FROM migrations WHERE migration = :migration LIMIT 1',
        );
        $statement->execute(['migration' => $migration]);

        return $statement->fetchColumn() !== false;
    }

    /**
     * Выполняет одну миграцию и записывает ее в таблицу migrations
     */
    private function runMigration(string $migrationFile): void
    {
        $migration = basename($migrationFile);
        $sql = file_get_contents($migrationFile);

        if ($sql === false) {
            throw new RuntimeException(sprintf('Unable to read migration file "%s".', $migrationFile));
        }

        $this->pdo->exec($sql);

        $statement = $this->pdo->prepare(
            'INSERT INTO migrations (migration, executed_at) VALUES (:migration, :executed_at)',
        );
        $statement->execute([
            'migration' => $migration,
            'executed_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);
    }
}
