<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DualStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    protected $dualStorageService;

    public function __construct(DualStorageService $dualStorageService)
    {
        $this->dualStorageService = $dualStorageService;
    }

    /**
     * 관리자 대시보드
     */
    public function dashboard()
    {
        $user = Auth::user();

        // 통계 데이터 수집
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_teams' => \App\Models\Team::count(),
            'total_matches' => \App\Models\GameMatch::count(),
            'active_users' => \App\Models\User::where('last_active_at', '>=', now()->subDays(7))->count(),
            'pending_teams' => \App\Models\Team::where('status', 'pending')->count(),
            'scheduled_matches' => \App\Models\GameMatch::where('status', '예정')->count(),
        ];

        // 최근 활동
        $recentUsers = \App\Models\User::latest()->limit(5)->get();
        $recentTeams = \App\Models\Team::with('owner')->latest()->limit(5)->get();
        $recentMatches = \App\Models\GameMatch::with(['homeTeam', 'awayTeam'])->latest()->limit(5)->get();

        // 시스템 상태
        $databaseStatus = $this->dualStorageService->getDatabaseStatus();
        $jsonBackupStatus = $this->dualStorageService->getJsonBackupStatus();

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentTeams',
            'recentMatches',
            'databaseStatus',
            'jsonBackupStatus'
        ));
    }

    /**
     * 시스템 설정
     */
    public function settings()
    {
        $settings = [
            'json_backup_enabled' => config('app.json_backup_enabled', true),
            'max_team_members' => config('app.max_team_members', 20),
            'match_duration_days' => config('app.match_duration_days', 30),
            'auto_approve_teams' => config('app.auto_approve_teams', false),
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * 시스템 설정 저장
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'json_backup_enabled' => 'boolean',
            'max_team_members' => 'integer|min:1|max:50',
            'match_duration_days' => 'integer|min:1|max:365',
            'auto_approve_teams' => 'boolean',
        ]);

        // 설정 파일 업데이트 (실제로는 .env 파일이나 config 파일을 수정해야 함)
        foreach ($validated as $key => $value) {
            config(["app.{$key}" => $value]);
        }

        return back()->with('success', '시스템 설정이 업데이트되었습니다.');
    }

    /**
     * 데이터 백업
     */
    public function backupData()
    {
        try {
            $this->dualStorageService->syncAllData();
            return response()->json(['success' => true, 'message' => '데이터 백업이 완료되었습니다.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => '백업 실패: ' . $e->getMessage()]);
        }
    }

    /**
     * 시스템 상태 확인
     */
    public function systemStatus()
    {
        $databaseStatus = $this->dualStorageService->getDatabaseStatus();
        $jsonBackupStatus = $this->dualStorageService->getJsonBackupStatus();

        return response()->json([
            'database' => $databaseStatus,
            'json_backup' => $jsonBackupStatus,
            'server' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'memory_usage' => memory_get_usage(true),
                'disk_free_space' => disk_free_space(storage_path()),
            ]
        ]);
    }
}
