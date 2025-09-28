<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--json : Export as JSON instead of copying SQLite file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database to backups folder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        // Create backups directory if it doesn't exist
        $backupPath = base_path('backups');
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_His');

        if ($this->option('json')) {
            // JSON backup
            $this->backupAsJson($backupPath, $timestamp);
        } else {
            // SQLite file backup
            $this->backupSqliteFile($backupPath, $timestamp);
        }

        // Also create a latest backup for easy restoration
        $this->createLatestBackup($backupPath);

        $this->info('✅ Database backup completed successfully!');
        return Command::SUCCESS;
    }

    /**
     * Backup SQLite database file
     */
    private function backupSqliteFile($backupPath, $timestamp)
    {
        $dbPath = database_path('database.sqlite');
        $backupFile = $backupPath . '/database_' . $timestamp . '.sqlite';

        if (File::exists($dbPath)) {
            File::copy($dbPath, $backupFile);
            $this->info('SQLite file backed up to: ' . $backupFile);
        } else {
            $this->error('SQLite database file not found!');
        }
    }

    /**
     * Backup database as JSON
     */
    private function backupAsJson($backupPath, $timestamp)
    {
        $tables = [
            'users',
            'regions',
            'sports',
            'teams',
            'team_members',
            'matches',
            'match_invitations',
        ];

        $backup = [];

        foreach ($tables as $table) {
            try {
                $backup[$table] = DB::table($table)->get()->toArray();
                $this->line("Backed up table: {$table} (" . count($backup[$table]) . " records)");
            } catch (\Exception $e) {
                $this->warn("Could not backup table {$table}: " . $e->getMessage());
            }
        }

        $jsonFile = $backupPath . '/database_' . $timestamp . '.json';
        File::put($jsonFile, json_encode($backup, JSON_PRETTY_PRINT));
        $this->info('JSON backup saved to: ' . $jsonFile);
    }

    /**
     * Create a copy as latest backup
     */
    private function createLatestBackup($backupPath)
    {
        // Copy SQLite file as latest
        $dbPath = database_path('database.sqlite');
        $latestSqlite = $backupPath . '/database_latest.sqlite';
        
        if (File::exists($dbPath)) {
            File::copy($dbPath, $latestSqlite);
        }

        // Also create latest JSON
        $this->backupAsJson($backupPath, 'latest');
    }
}