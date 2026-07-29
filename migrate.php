<?php

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    header('HTTP/1.1 403 Forbidden');
    echo "Access denied: This script can only be run from the command line.\n";
    exit(1);
}

if (!defined('MIGRATOR_BASE_DIR')) {
    define('MIGRATOR_BASE_DIR', file_exists(__DIR__ . '/vendor/autoload.php') ? __DIR__ : dirname(__DIR__));
}

require MIGRATOR_BASE_DIR . '/vendor/autoload.php';

// Enable error display for debugging
ini_set('display_errors', '1');
error_reporting(E_ALL);

try {
    $app = new \Core\Application(MIGRATOR_BASE_DIR);
} catch (\Throwable $e) {
    echo "\e[31mFailed to boot application: " . $e->getMessage() . "\e[0m\n";
    exit(1);
}

$db = \Core\Database::connection();

// Helper functions for terminal coloring
function info(string $msg): void {
    echo "\e[36m" . $msg . "\e[0m\n";
}

function success(string $msg): void {
    echo "\e[32m✔ " . $msg . "\e[0m\n";
}

function warning(string $msg): void {
    echo "\e[33m⚡ " . $msg . "\e[0m\n";
}

function error(string $msg, ?\Throwable $e = null): void {
    echo "\e[31m❌ " . $msg . "\e[0m\n";
    if ($e) {
        echo "\e[31m" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\e[0m\n";
    }
}

// Ensure cms schema and migration table exist
function initMigrationsTable(\PDO $db): void {
    $db->exec("CREATE SCHEMA IF NOT EXISTS cms");
    $db->exec("
        CREATE TABLE IF NOT EXISTS cms.migrations (
            id SERIAL PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

$command = $argv[1] ?? 'up';

echo "\n\e[1;35m--- JNTO Framework Migrator ---\e[0m\n\n";

switch ($command) {
    case 'up':
    case 'migrate':
        initMigrationsTable($db);
        runMigrations($db);
        break;

    case 'rollback':
        initMigrationsTable($db);
        rollbackMigrations($db);
        break;

    case 'fresh':
        freshDatabase($db);
        break;

    case 'status':
        initMigrationsTable($db);
        showStatus($db);
        break;

    default:
        error("Unknown command: '$command'");
        echo "Available commands:\n";
        echo "  php migrate.php (or php migrate.php up)  : Run all pending migrations\n";
        echo "  php migrate.php rollback                 : Roll back the last batch of migrations\n";
        echo "  php migrate.php fresh                    : Drop all tables, recreate schema and run all migrations\n";
        echo "  php migrate.php status                   : Show the current status of all migrations\n";
        break;
}

function getMigrationFiles(): array {
    $dir = MIGRATOR_BASE_DIR . '/database/migrations';
    if (!is_dir($dir)) {
        return [];
    }

    $files = glob($dir . '/*.php') ?: [];
    sort($files);

    $migrations = [];
    foreach ($files as $file) {
        $migrations[basename($file, '.php')] = $file;
    }

    return $migrations;
}


function runMigrations(\PDO $db): void {
    info("Running pending migrations...");

    $files = getMigrationFiles();
    if (empty($files)) {
        warning("No migration files found in database/migrations/");
        return;
    }

    // Get applied migrations
    $stmt = $db->query("SELECT migration FROM cms.migrations");
    $applied = $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];

    $pending = array_diff(array_keys($files), $applied);

    if (empty($pending)) {
        success("Nothing to migrate. Database is up to date.");
        return;
    }

    // Determine batch number
    $batchStmt = $db->query("SELECT MAX(batch) FROM cms.migrations");
    $maxBatch = (int) $batchStmt->fetchColumn();
    $batch = $maxBatch + 1;

    foreach ($pending as $name) {
        $file = $files[$name];
        info("Migrating: $name");

        try {
            $migration = require $file;
            if (is_object($migration) && method_exists($migration, 'up')) {
                $migration->up();
            } else {
                throw new \RuntimeException("Migration file does not return a class instance with an up() method.");
            }

            $insert = $db->prepare("INSERT INTO cms.migrations (migration, batch) VALUES (:migration, :batch)");
            $insert->execute(['migration' => $name, 'batch' => $batch]);

            success("Migrated:  $name");
        } catch (\Throwable $e) {
            error("Failed migrating $name", $e);
            exit(1);
        }
    }
}

function rollbackMigrations(\PDO $db): void {
    info("Rolling back migrations...");

    // Find latest batch
    $batchStmt = $db->query("SELECT MAX(batch) FROM cms.migrations");
    $maxBatch = $batchStmt->fetchColumn();

    if ($maxBatch === null || $maxBatch === false) {
        warning("Nothing to rollback.");
        return;
    }

    $maxBatch = (int) $maxBatch;

    // Get migrations in this batch in descending order
    $stmt = $db->prepare("SELECT migration FROM cms.migrations WHERE batch = :batch ORDER BY id DESC");
    $stmt->execute(['batch' => $maxBatch]);
    $migrations = $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];

    if (empty($migrations)) {
        warning("No migrations found in batch $maxBatch.");
        return;
    }

    $files = getMigrationFiles();

    foreach ($migrations as $name) {
        info("Rolling back: $name");

        if (!isset($files[$name])) {
            error("Migration file not found for $name. Skipping.");
            continue;
        }

        try {
            $migration = require $files[$name];
            if (is_object($migration) && method_exists($migration, 'down')) {
                $migration->down();
            } else {
                throw new \RuntimeException("Migration file does not return a class instance with a down() method.");
            }

            $delete = $db->prepare("DELETE FROM cms.migrations WHERE migration = :migration");
            $delete->execute(['migration' => $name]);

            success("Rolled back:  $name");
        } catch (\Throwable $e) {
            error("Failed rolling back $name", $e);
            exit(1);
        }
    }
}

function freshDatabase(\PDO $db): void {
    info("Tearing down the database (fresh)...");
    
    try {
        $db->exec("DROP SCHEMA IF EXISTS cms CASCADE");
        success("Dropped schema cms");
    } catch (\Throwable $e) {
        error("Failed to drop schema", $e);
        exit(1);
    }

    initMigrationsTable($db);
    runMigrations($db);
}

function showStatus(\PDO $db): void {
    info("Checking migration status...");

    $files = getMigrationFiles();
    if (empty($files)) {
        warning("No migration files found in database/migrations/");
        return;
    }

    // Get applied migrations
    $stmt = $db->query("SELECT migration, batch, created_at FROM cms.migrations ORDER BY id ASC");
    $appliedRows = [];
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $appliedRows[$row['migration']] = $row;
    }

    echo str_pad("Migration Name", 50) . " | Status    | Batch | Applied At\n";
    echo str_repeat("-", 90) . "\n";

    foreach ($files as $name => $file) {
        if (isset($appliedRows[$name])) {
            $row = $appliedRows[$name];
            $status = "\e[32mRan\e[0m";
            $batch = (string) $row['batch'];
            $date = $row['created_at'];
        } else {
            $status = "\e[31mPending\e[0m";
            $batch = "N/A";
            $date = "N/A";
        }
        
        echo str_pad($name, 50) . " | " . str_pad($status, 20) . " | " . str_pad($batch, 5) . " | $date\n";
    }
    echo "\n";
}
