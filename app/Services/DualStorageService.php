<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DualStorageService
{
    private $jsonStorageService;
    private $enableJsonBackup;

    public function __construct()
    {
        $this->jsonStorageService = new JsonStorageService();
        $this->enableJsonBackup = config('app.json_backup_enabled', true);
    }

    /**
     * 데이터를 데이터베이스와 JSON에 동시 저장
     */
    public function save($model, $data, $filename = null)
    {
        try {
            DB::beginTransaction();

            // 1. 데이터베이스에 저장
            $savedModel = $model::create($data);

            // 2. JSON 백업 활성화된 경우 백업
            if ($this->enableJsonBackup) {
                $this->jsonStorageService->saveModelData($savedModel, $filename);
            }

            DB::commit();

            Log::info("이중 저장 완료: " . get_class($model) . " ID: {$savedModel->id}");
            return $savedModel;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("이중 저장 실패: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 데이터베이스와 JSON에서 동시 업데이트
     */
    public function update($model, $id, $data, $filename = null)
    {
        try {
            DB::beginTransaction();

            // 1. 데이터베이스 업데이트
            $updatedModel = $model::findOrFail($id);
            $updatedModel->update($data);

            // 2. JSON 백업 업데이트
            if ($this->enableJsonBackup) {
                $this->jsonStorageService->saveModelData($updatedModel, $filename);
            }

            DB::commit();

            Log::info("이중 업데이트 완료: " . get_class($model) . " ID: {$id}");
            return $updatedModel;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("이중 업데이트 실패: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 데이터베이스와 JSON에서 동시 삭제
     */
    public function delete($model, $id, $filename = null)
    {
        try {
            DB::beginTransaction();

            // 1. 데이터베이스에서 삭제
            $modelToDelete = $model::findOrFail($id);
            $modelToDelete->delete();

            // 2. JSON 백업에서 삭제
            if ($this->enableJsonBackup && $filename) {
                $this->jsonStorageService->deleteModelData($filename);
            }

            DB::commit();

            Log::info("이중 삭제 완료: " . get_class($model) . " ID: {$id}");
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("이중 삭제 실패: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 전체 데이터 동기화
     */
    public function syncAllData()
    {
        try {
            $this->jsonStorageService->backupAllData();
            Log::info("전체 데이터 동기화 완료");
            return true;
        } catch (\Exception $e) {
            Log::error("전체 데이터 동기화 실패: " . $e->getMessage());
            return false;
        }
    }

    /**
     * JSON에서 데이터베이스로 복원
     */
    public function restoreFromJson($filename)
    {
        try {
            return $this->jsonStorageService->restoreFromBackup($filename);
        } catch (\Exception $e) {
            Log::error("JSON 복원 실패: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 데이터베이스 상태 확인
     */
    public function getDatabaseStatus()
    {
        try {
            $status = [
                'users' => \App\Models\User::count(),
                'teams' => \App\Models\Team::count(),
                'matches' => \App\Models\GameMatch::count(),
                'team_members' => \App\Models\TeamMember::count(),
                'regions' => \App\Models\Region::count(),
                'sports' => \App\Models\Sport::count(),
                'last_updated' => now()->toISOString(),
            ];

            return $status;
        } catch (\Exception $e) {
            Log::error("데이터베이스 상태 확인 실패: " . $e->getMessage());
            return null;
        }
    }

    /**
     * JSON 백업 상태 확인
     */
    public function getJsonBackupStatus()
    {
        try {
            $backupFiles = Storage::files('json_data');
            $latestBackup = null;

            if (!empty($backupFiles)) {
                $latestBackup = end($backupFiles);
                $latestBackup = str_replace('json_data/', '', $latestBackup);
                $latestBackup = str_replace('.json', '', $latestBackup);
            }

            return [
                'enabled' => $this->enableJsonBackup,
                'backup_count' => count($backupFiles),
                'latest_backup' => $latestBackup,
                'storage_path' => 'json_data',
            ];
        } catch (\Exception $e) {
            Log::error("JSON 백업 상태 확인 실패: " . $e->getMessage());
            return null;
        }
    }
}
