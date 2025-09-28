<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DualStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    protected $dualStorageService;

    public function __construct(DualStorageService $dualStorageService)
    {
        $this->dualStorageService = $dualStorageService;
    }

    /**
     * 사용자 목록
     */
    public function index(Request $request)
    {
        $query = User::with(['ownedTeams', 'teamMemberships.team']);

        // 검색 필터
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%");
            });
        }

        // 역할 필터
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 상태 필터
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('last_active_at', '>=', now()->subDays(7));
                    break;
                case 'inactive':
                    $query->where('last_active_at', '<', now()->subDays(7))
                          ->orWhereNull('last_active_at');
                    break;
                case 'team_owners':
                    $query->where('role', 'team_owner');
                    break;
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        $roles = ['user', 'team_owner', 'admin'];
        $statuses = ['active', 'inactive', 'team_owners'];

        return view('admin.users.index', compact('users', 'roles', 'statuses'));
    }

    /**
     * 사용자 상세 정보
     */
    public function show(User $user)
    {
        $user->load([
            'ownedTeams.members.user',
            'teamMemberships.team.owner',
            'createdMatches.homeTeam',
            'createdMatches.awayTeam'
        ]);

        $recentActivity = collect();

        // 최근 팀 활동
        $recentActivity = $recentActivity->merge(
            $user->ownedTeams->map(function($team) {
                return [
                    'type' => 'team_created',
                    'message' => "팀 '{$team->team_name}' 생성",
                    'date' => $team->created_at,
                ];
            })
        );

        // 최근 경기 활동
        $recentActivity = $recentActivity->merge(
            $user->createdMatches->map(function($match) {
                return [
                    'type' => 'match_created',
                    'message' => "경기 '{$match->home_team_name} vs {$match->away_team_name}' 생성",
                    'date' => $match->created_at,
                ];
            })
        );

        $recentActivity = $recentActivity->sortByDesc('date')->take(10);

        return view('admin.users.show', compact('user', 'recentActivity'));
    }

    /**
     * 사용자 편집 폼
     */
    public function edit(User $user)
    {
        $roles = ['user', 'team_owner', 'admin'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * 사용자 정보 업데이트
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nickname' => 'required|string|max:50',
            'role' => 'required|in:user,team_owner,admin',
            'city' => 'nullable|string|max:50',
            'district' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // 비밀번호가 제공된 경우에만 업데이트
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        try {
            $this->dualStorageService->update(User::class, $user->id, $validated, "user_{$user->id}");

            return redirect()->route('admin.users.show', $user)
                ->with('success', '사용자 정보가 업데이트되었습니다.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', '사용자 정보 업데이트 실패: ' . $e->getMessage());
        }
    }

    /**
     * 사용자 삭제
     */
    public function destroy(User $user)
    {
        // 관리자는 자기 자신을 삭제할 수 없음
        if ($user->id === auth()->id()) {
            return back()->with('error', '자기 자신을 삭제할 수 없습니다.');
        }

        // 팀 소유자인 경우 팀도 함께 처리
        if ($user->role === 'team_owner') {
            $ownedTeams = $user->ownedTeams;
            foreach ($ownedTeams as $team) {
                // 팀 멤버들 처리
                $team->members()->delete();
                // 팀 관련 경기들 처리
                $team->homeMatches()->delete();
                $team->awayMatches()->delete();
                // 팀 삭제
                $team->delete();
            }
        }

        try {
            $this->dualStorageService->delete(User::class, $user->id, "user_{$user->id}");

            return redirect()->route('admin.users.index')
                ->with('success', '사용자가 삭제되었습니다.');

        } catch (\Exception $e) {
            return back()->with('error', '사용자 삭제 실패: ' . $e->getMessage());
        }
    }

    /**
     * 사용자에게 경고 메시지 전송
     */
    public function sendWarning(Request $request, User $user)
    {
        $validated = $request->validate([
            'warning_message' => 'required|string|max:500',
            'warning_type' => 'required|in:minor,major,severe',
        ]);

        // 경고 기록 저장 (실제로는 별도 테이블에 저장해야 함)
        $warning = [
            'user_id' => $user->id,
            'message' => $validated['warning_message'],
            'type' => $validated['warning_type'],
            'admin_id' => auth()->id(),
            'created_at' => now(),
        ];

        // JSON으로 경고 기록 저장
        $jsonService = new \App\Services\JsonStorageService();
        $jsonService->save("warning_{$user->id}_" . time(), $warning);

        // 이메일로 경고 전송 (실제 구현 시)
        // Mail::to($user->email)->send(new WarningMail($validated['warning_message']));

        return back()->with('success', '경고 메시지가 전송되었습니다.');
    }

    /**
     * 사용자 계정 비활성화/활성화
     */
    public function toggleStatus(User $user)
    {
        $newStatus = $user->email_verified_at ? null : now();

        try {
            $this->dualStorageService->update(User::class, $user->id, [
                'email_verified_at' => $newStatus
            ], "user_{$user->id}");

            $statusText = $newStatus ? '활성화' : '비활성화';

            return back()->with('success', "사용자 계정이 {$statusText}되었습니다.");

        } catch (\Exception $e) {
            return back()->with('error', '계정 상태 변경 실패: ' . $e->getMessage());
        }
    }

    /**
     * 사용자 통계
     */
    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('last_active_at', '>=', now()->subDays(7))->count(),
            'team_owners' => User::where('role', 'team_owner')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'users_by_city' => User::selectRaw('city, count(*) as count')
                ->whereNotNull('city')
                ->groupBy('city')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('admin.users.statistics', compact('stats'));
    }
}
