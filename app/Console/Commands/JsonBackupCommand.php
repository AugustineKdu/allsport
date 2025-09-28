<?php

namespace App\Console\Commands;

use App\Services\JsonStorageService;
use Illuminate\Console\Command;

class JsonBackupCommand extends Command
{
    protected $signature = 'json:backup {--restore= : Restore from backup file}';
    protected $description = '백업 또는 복원 JSON 데이터';

    public function handle()
    {
        $jsonService = new JsonStorageService();

        if ($restoreFile = $this->option('restore')) {
            $this->info("백업 파일에서 복원 중: {$restoreFile}");

            if ($jsonService->restoreFromBackup($restoreFile)) {
                $this->info("✅ 복원 완료!");
            } else {
                $this->error("❌ 복원 실패!");
            }
        } else {
            $this->info("JSON 데이터 백업 중...");

            if ($jsonService->backupAllData()) {
                $this->info("✅ 백업 완료!");
            } else {
                $this->error("❌ 백업 실패!");
            }
        }
    }
}
