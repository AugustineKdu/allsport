<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class JsonStorageService
{
    private $storagePath;

    public function __construct()
    {
        $this->storagePath = 'json_data';
    }

    /**
     * 데이터를 JSON 파일로 저장
     */
    public function save($filename, $data)
    {
        try {
            $path = $this->storagePath . '/' . $filename . '.json';
            $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            Storage::put($path, $jsonData);

            Log::info("JSON 데이터 저장 완료: {$path}");
            return true;
        } catch (\Exception $e) {
            Log::error("JSON 데이터 저장 실패: " . $e->getMessage());
            return false;
        }
    }

    /**
     * JSON 파일에서 데이터 로드
     */
    public function load($filename)
    {
        try {
            $path = $this->storagePath . '/' . $filename . '.json';

            if (!Storage::exists($path)) {
                return null;
            }

            $jsonData = Storage::get($path);
            $data = json_decode($jsonData, true);

            return $data;
        } catch (\Exception $e) {
            Log::error("JSON 데이터 로드 실패: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 모든 데이터를 JSON으로 백업
     */
    public function backupAllData()
    {
        try {
            $backupData = [
                'timestamp' => now()->toISOString(),
                'users' => \App\Models\User::all()->toArray(),
                'teams' => \App\Models\Team::with(['owner', 'members'])->get()->toArray(),
                'matches' => \App\Models\GameMatch::with(['homeTeam', 'awayTeam'])->get()->toArray(),
                'team_members' => \App\Models\TeamMember::with(['user', 'team'])->get()->toArray(),
                'regions' => \App\Models\Region::all()->toArray(),
                'sports' => \App\Models\Sport::all()->toArray(),
            ];

            $filename = 'backup_' . now()->format('Y-m-d_H-i-s');
            return $this->save($filename, $backupData);
        } catch (\Exception $e) {
            Log::error("전체 데이터 백업 실패: " . $e->getMessage());
            return false;
        }
    }

    /**
     * JSON 백업에서 데이터 복원
     */
    public function restoreFromBackup($filename)
    {
        try {
            $backupData = $this->load($filename);

            if (!$backupData) {
                return false;
            }

            // 기존 데이터 삭제 (주의!)
            \App\Models\TeamMember::truncate();
            \App\Models\GameMatch::truncate();
            \App\Models\Team::truncate();
            \App\Models\User::truncate();

            // 데이터 복원
            if (isset($backupData['users'])) {
                foreach ($backupData['users'] as $userData) {
                    unset($userData['id']); // ID는 자동 생성
                    \App\Models\User::create($userData);
                }
            }

            if (isset($backupData['teams'])) {
                foreach ($backupData['teams'] as $teamData) {
                    unset($teamData['id']);
                    \App\Models\Team::create($teamData);
                }
            }

            if (isset($backupData['matches'])) {
                foreach ($backupData['matches'] as $matchData) {
                    unset($matchData['id']);
                    \App\Models\GameMatch::create($matchData);
                }
            }

            if (isset($backupData['team_members'])) {
                foreach ($backupData['team_members'] as $memberData) {
                    unset($memberData['id']);
                    \App\Models\TeamMember::create($memberData);
                }
            }

            Log::info("JSON 백업에서 데이터 복원 완료: {$filename}");
            return true;
        } catch (\Exception $e) {
            Log::error("JSON 백업 복원 실패: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 팀 데이터를 JSON으로 저장
     */
    public function saveTeamData($teamId)
    {
        try {
            $team = \App\Models\Team::with(['owner', 'members.user', 'homeMatches', 'awayMatches'])
                ->find($teamId);

            if (!$team) {
                return false;
            }

            $filename = 'team_' . $teamId;
            return $this->save($filename, $team->toArray());
        } catch (\Exception $e) {
            Log::error("팀 데이터 저장 실패: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 경기 데이터를 JSON으로 저장
     */
    public function saveMatchData($matchId)
    {
        try {
            $match = \App\Models\GameMatch::with(['homeTeam', 'awayTeam', 'creator'])
                ->find($matchId);

            if (!$match) {
                return false;
            }

            $filename = 'match_' . $matchId;
            return $this->save($filename, $match->toArray());
        } catch (\Exception $e) {
            Log::error("경기 데이터 저장 실패: " . $e->getMessage());
            return false;
        }
    }
}
