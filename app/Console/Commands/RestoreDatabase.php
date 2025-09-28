<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class RestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:database {file? : Backup file name (defaults to latest)} {--json : Restore from JSON file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database from backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database restoration...');

        $backupPath = base_path('backups');
        $fileName = $this->argument('file') ?? 'latest';

        if ($this->option('json')) {
            $backupFile = $backupPath . '/database_' . $fileName . '.json';
            $this->restoreFromJson($backupFile);
        } else {
            $backupFile = $backupPath . '/database_' . $fileName . '.sqlite';
            $this->restoreFromSqlite($backupFile);
        }

        $this->info('✅ Database restoration completed successfully!');
        return Command::SUCCESS;
    }

    /**
     * Restore from SQLite file
     */
    private function restoreFromSqlite($backupFile)
    {
        if (!File::exists($backupFile)) {
            $this->error("Backup file not found: {$backupFile}");
            return;
        }

        $dbPath = database_path('database.sqlite');

        // Create backup of current database
        if (File::exists($dbPath)) {
            $currentBackup = database_path('database_before_restore.sqlite');
            File::copy($dbPath, $currentBackup);
            $this->info('Current database backed up to: ' . $currentBackup);
        }

        // Restore database
        File::copy($backupFile, $dbPath);
        $this->info('Database restored from: ' . $backupFile);
    }

    /**
     * Restore from JSON file
     */
    private function restoreFromJson($backupFile)
    {
        if (!File::exists($backupFile)) {
            $this->error("Backup file not found: {$backupFile}");
            return;
        }

        $data = json_decode(File::get($backupFile), true);

        if (!$data) {
            $this->error('Invalid JSON file!');
            return;
        }

        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Order tables to avoid foreign key issues
        $tableOrder = [
            'users',
            'regions',
            'sports',
            'teams',
            'team_members',
            'matches',
            'match_invitations',
        ];

        // Clear existing data
        $this->info('Clearing existing data...');
        foreach (array_reverse($tableOrder) as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // Restore data
        $this->info('Restoring data...');
        foreach ($tableOrder as $table) {
            if (isset($data[$table]) && count($data[$table]) > 0) {
                // Convert objects to arrays
                $records = array_map(function($record) {
                    return (array) $record;
                }, $data[$table]);

                DB::table($table)->insert($records);
                $this->line("Restored table: {$table} (" . count($records) . " records)");
            }
        }

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        $this->info('Data restored from: ' . $backupFile);
    }
}