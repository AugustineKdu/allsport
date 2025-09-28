<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Region;
use App\Models\Sport;
use App\Helpers\RegionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    /**
     * Display a listing of teams.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        $query = Team::with(['owner', 'approvedMembers']);

        // Apply filters with region mapping
        if ($request->filled('city')) {
            $dbCity = RegionHelper::standardToDatabase($request->city);
            $query->where('city', $dbCity);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('sport')) {
            $query->where('sport', $request->sport);
        }

        if ($request->filled('search')) {
            $query->where('team_name', 'like', '%' . $request->search . '%');
        }

        // Default filters based on user preferences
        if (!$request->hasAny(['city', 'district', 'sport'])) {
            $query->where('city', $user->city)
                  ->where('sport', $user->selected_sport);
        }

        $teams = $query->orderBy('points', 'desc')
                       ->orderBy('wins', 'desc')
                       ->paginate(12);

        // Get filter options
        // Get cities from database and convert to standard names
        $dbCities = Region::active()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        // Convert to standard region names for display
        $cities = $dbCities->map(function($city) {
            return RegionHelper::databaseToStandard($city);
        })->unique()->sort()->values();

        // Handle district filtering with region mapping
        $dbCity = $request->city ? RegionHelper::standardToDatabase($request->city) : null;
        $districts = Region::active()
            ->when($dbCity, function ($q) use ($dbCity) {
                return $q->where('city', $dbCity);
            })
            ->orderBy('district')
            ->pluck('district');

        $sports = Sport::active()->pluck('sport_name');

        return view('teams.index', compact('teams', 'currentTeam', 'cities', 'districts', 'sports'));
    }

    /**
     * Show the form for creating a new team.
     */
    public function create()
    {
        $user = Auth::user();

        // Check if user already owns a team
        if ($user->ownedTeams()->count() > 0) {
            return redirect()->route('teams.index')
                ->with('error', '이미 팀을 소유하고 있습니다.');
        }

        // Get all regions for selection
        $regions = Region::active()
            ->orderBy('city')
            ->orderBy('district')
            ->get();

        $sports = Sport::active()->get();

        return view('teams.create', compact('regions', 'sports'));
    }

    /**
     * Store a newly created team.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_name' => 'required|string|max:50',
            'district' => 'required|string|exists:regions,district',
            'sport' => 'required|string|exists:sports,sport_name',
        ]);

        // Find the selected region to get the city
        $region = Region::where('district', $validated['district'])
            ->where('is_active', true)
            ->first();

        if (!$region) {
            return back()->withErrors(['district' => '선택하신 지역이 유효하지 않습니다.']);
        }

        DB::beginTransaction();

        try {
            $team = Team::create([
                'team_name' => $validated['team_name'],
                'city' => $region->city,
                'district' => $validated['district'],
                'sport' => $validated['sport'],
                'owner_user_id' => Auth::id(),
            ]);

            // Add owner as team member
            TeamMember::create([
                'team_id' => $team->id,
                'user_id' => Auth::id(),
                'role' => 'owner',
                'status' => 'approved',
                'joined_at' => now(),
            ]);

            // Update user role
            Auth::user()->update(['role' => 'team_owner']);

            DB::commit();

            return redirect()->route('teams.show', $team->slug)
                ->with('success', '팀이 성공적으로 생성되었습니다!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($e->getCode() == 23000) { // Duplicate entry
                return back()->withInput()
                    ->withErrors(['team_name' => '이미 존재하는 팀 이름입니다.']);
            }

            return back()->withInput()
                ->with('error', '팀 생성 중 오류가 발생했습니다.');
        }
    }

    /**
     * Display the specified team.
     */
    public function show($slug)
    {
        $team = Team::with(['owner', 'approvedMembers.user', 'homeMatches.awayTeam', 'awayMatches.homeTeam'])
            ->where('slug', $slug)
            ->firstOrFail();

        $user = Auth::user();
        $membership = null;

        if ($user) {
            $membership = TeamMember::where('team_id', $team->id)
                ->where('user_id', $user->id)
                ->first();
        }

        // Get online members
        $onlineMembers = $team->approvedMembers()
            ->online()
            ->with('user')
            ->get();

        // Get pending applications count for team owner
        $pendingCount = 0;
        if ($user && $team->owner_user_id === $user->id) {
            $pendingCount = $team->pendingMembers()->count();
        }

        // Get upcoming matches
        $upcomingMatches = $team->allMatches()
            ->upcoming()
            ->with(['homeTeam', 'awayTeam'])
            ->limit(5)
            ->get();

        // Get recent matches
        $recentMatches = $team->allMatches()
            ->completed()
            ->orderBy('match_date', 'desc')
            ->with(['homeTeam', 'awayTeam'])
            ->limit(5)
            ->get();

        return view('teams.show', compact('team', 'membership', 'onlineMembers', 'upcomingMatches', 'recentMatches', 'pendingCount'));
    }

    /**
     * Apply to join a team.
     */
    public function apply(Request $request, $slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        // Check if user already has a team
        if ($user->currentTeam()) {
            return back()->with('error', '이미 다른 팀에 소속되어 있습니다.');
        }

        // Check if already applied
        $existingMembership = TeamMember::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingMembership) {
            return back()->with('error', '이미 가입 신청을 했거나 팀 멤버입니다.');
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:255',
        ]);

        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => 'member',
            'status' => 'pending',
            'message' => $validated['message'] ?? null,
        ]);

        return back()->with('success', '팀 가입 신청이 완료되었습니다. 팀 관리자의 승인을 기다려주세요.');
    }

    /**
     * Leave a team.
     */
    public function leave($slug)
    {
        $team = Team::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        $membership = TeamMember::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();

        if (!$membership) {
            return back()->with('error', '이 팀의 멤버가 아닙니다.');
        }

        if ($membership->role === 'owner') {
            return back()->with('error', '팀 소유자는 팀을 떠날 수 없습니다.');
        }

        $membership->leave();

        return redirect()->route('teams.index')
            ->with('success', '팀에서 성공적으로 탈퇴했습니다.');
    }

    /**
     * Show team management page for owners.
     */
    public function manage($slug)
    {
        $team = Team::with(['owner', 'approvedMembers.user', 'pendingMembers.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $user = Auth::user();

        // Check if user is team owner
        if ($team->owner_user_id !== $user->id) {
            return redirect()->route('teams.show', $slug)
                ->with('error', '팀 관리 권한이 없습니다.');
        }

        $pendingApplications = $team->pendingMembers()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teams.manage', compact('team', 'pendingApplications'));
    }

    /**
     * Approve a team member application.
     */
    public function approve($slug, TeamMember $member)
    {
        $team = Team::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        // Check if user is team owner
        if ($team->owner_user_id !== $user->id) {
            return back()->with('error', '권한이 없습니다.');
        }

        // Check if member belongs to this team and is pending
        if ($member->team_id !== $team->id || $member->status !== 'pending') {
            return back()->with('error', '잘못된 요청입니다.');
        }

        $member->approve();

        return back()->with('success', $member->user->nickname . '님의 가입 신청을 승인했습니다.');
    }

    /**
     * Reject a team member application.
     */
    public function reject($slug, TeamMember $member)
    {
        $team = Team::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        // Check if user is team owner
        if ($team->owner_user_id !== $user->id) {
            return back()->with('error', '권한이 없습니다.');
        }

        // Check if member belongs to this team and is pending
        if ($member->team_id !== $team->id || $member->status !== 'pending') {
            return back()->with('error', '잘못된 요청입니다.');
        }

        $member->reject();

        return back()->with('success', $member->user->nickname . '님의 가입 신청을 거부했습니다.');
    }

    /**
     * Kick a team member.
     */
    public function kick($slug, TeamMember $member)
    {
        $team = Team::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        // Check if user is team owner
        if ($team->owner_user_id !== $user->id) {
            return back()->with('error', '권한이 없습니다.');
        }

        // Check if member belongs to this team and is approved
        if ($member->team_id !== $team->id || $member->status !== 'approved') {
            return back()->with('error', '잘못된 요청입니다.');
        }

        // Cannot kick team owner
        if ($member->role === 'owner') {
            return back()->with('error', '팀 소유자는 퇴출할 수 없습니다.');
        }

        $memberName = $member->user->nickname;
        $member->leave();

        return back()->with('success', $memberName . '님을 팀에서 퇴출했습니다.');
    }

    /**
     * Get districts for a given city (AJAX endpoint).
     */
    public function getDistricts($city)
    {
        // Convert standard region name to database name
        $dbCity = RegionHelper::standardToDatabase($city);

        $districts = Region::active()
            ->where('city', $dbCity)
            ->orderBy('district')
            ->pluck('district');

        return response()->json($districts);
    }
}
