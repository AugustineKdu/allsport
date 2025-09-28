<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\Team;
use App\Models\MatchInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    /**
     * Display a listing of matches.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        $query = GameMatch::with(['homeTeam', 'awayTeam']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('sport')) {
            $query->where('sport', $request->sport);
        }

        // Default: show matches in user's region and sport
        if (!$request->hasAny(['city', 'district', 'sport'])) {
            $query->where('city', $user->city)
                  ->where('sport', $user->selected_sport);
        }

        $matches = $query->orderBy('match_date', 'desc')
                         ->orderBy('match_time', 'desc')
                         ->paginate(12);

        // Get available teams for match creation (all teams with same sport)
        $availableTeams = collect();
        $sentInvitations = collect();
        $receivedInvitations = collect();

        if ($currentTeam) {
            $availableTeams = Team::where('sport', $currentTeam->sport)
                ->where('id', '!=', $currentTeam->id)
                ->with(['owner'])
                ->get();

            // Get pending invitations sent by this team
            $sentInvitations = MatchInvitation::where('inviting_team_id', $currentTeam->id)
                ->where('status', 'pending')
                ->with(['invitedTeam'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Get pending invitations received by this team
            $receivedInvitations = MatchInvitation::where('invited_team_id', $currentTeam->id)
                ->where('status', 'pending')
                ->with(['invitingTeam'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Get filter options
        $statusOptions = ['예정', '진행중', '완료', '취소'];
        $cityOptions = GameMatch::distinct()->pluck('city')->filter()->sort()->values();
        $sportOptions = GameMatch::distinct()->pluck('sport')->filter()->sort()->values();

        // Initialize empty collections for removed match request system
        $myRequests = collect();
        $incomingRequests = collect();

        return view('matches.index', compact('matches', 'currentTeam', 'availableTeams', 'sentInvitations', 'receivedInvitations', 'myRequests', 'incomingRequests', 'statusOptions', 'cityOptions', 'sportOptions'));
    }

    /**
     * Show the form for creating a new match.
     */
    public function create()
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        if (!$currentTeam) {
            return redirect()->route('teams.index')
                ->with('error', '경기를 생성하려면 팀에 가입해야 합니다.');
        }

        // Get all teams for opponent selection
        $availableTeams = Team::where('sport', $currentTeam->sport)
            ->where('id', '!=', $currentTeam->id)
            ->with(['owner'])
            ->get();

        return view('matches.create', compact('currentTeam', 'availableTeams'));
    }

    /**
     * Store a newly created match.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        if (!$currentTeam) {
            return redirect()->route('teams.index')
                ->with('error', '경기를 생성하려면 팀에 가입해야 합니다.');
        }

        $validated = $request->validate([
            'away_team_id' => 'required|exists:teams,id',
            'match_date' => 'required|date|after:today',
            'match_time' => 'required',
            'venue' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $awayTeam = Team::findOrFail($validated['away_team_id']);

        // Verify same sport
        if ($awayTeam->sport !== $currentTeam->sport) {
            return back()->withErrors(['away_team_id' => '같은 스포츠의 팀과만 경기할 수 있습니다.']);
        }

        $match = GameMatch::create([
            'sport' => $currentTeam->sport,
            'city' => $currentTeam->city,
            'district' => $currentTeam->district,
            'home_team_id' => $currentTeam->id,
            'home_team_name' => $currentTeam->team_name,
            'away_team_id' => $awayTeam->id,
            'away_team_name' => $awayTeam->team_name,
            'match_date' => $validated['match_date'],
            'match_time' => $validated['match_time'],
            'venue' => $validated['venue'],
            'status' => '예정',
            'notes' => $validated['notes'],
            'created_by' => $user->id,
        ]);

        return redirect()->route('matches.show', $match)
            ->with('success', '경기가 성공적으로 생성되었습니다.');
    }

    /**
     * Display the specified match.
     */
    public function show(GameMatch $match)
    {
        $match->load(['homeTeam', 'awayTeam', 'creator']);
        return view('matches.show', compact('match'));
    }

    /**
     * Apply to join a match.
     */
    public function apply(Request $request, GameMatch $match)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // Validation
        if (!$currentTeam) {
            return back()->withErrors(['error' => '팀에 가입해야 경기에 신청할 수 있습니다.']);
        }

        if ($match->status !== '예정') {
            return back()->withErrors(['error' => '신청 가능한 경기가 아닙니다.']);
        }

        if ($match->sport !== $currentTeam->sport) {
            return back()->withErrors(['error' => '다른 스포츠 종목의 경기입니다.']);
        }

        if ($match->city !== $currentTeam->city || $match->district !== $currentTeam->district) {
            return back()->withErrors(['error' => '다른 지역의 경기입니다.']);
        }

        if ($match->home_team_id === $currentTeam->id || $match->away_team_id === $currentTeam->id) {
            return back()->withErrors(['error' => '이미 참여하는 경기입니다.']);
        }

        // Check if there's an empty slot
        if ($match->away_team_id === null) {
            // Apply as away team
            DB::transaction(function () use ($match, $currentTeam) {
                $match->update([
                    'away_team_id' => $currentTeam->id,
                    'away_team_name' => $currentTeam->team_name,
                ]);
            });

            return back()->with('success', '경기 신청이 완료되었습니다! 원정팀으로 참여합니다.');
        } else {
            return back()->withErrors(['error' => '이 경기는 이미 팀이 모두 확정되었습니다.']);
        }
    }

    /**
     * Show the form for editing match result.
     */
    public function editResult(GameMatch $match)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // Only home team owner can edit result
        if (!$currentTeam || $match->home_team_id !== $currentTeam->id || $currentTeam->owner_user_id !== $user->id) {
            return back()->with('error', '경기 결과 입력 권한이 없습니다.');
        }

        // Can only edit if match is scheduled or ongoing
        if (!in_array($match->status, ['예정', '진행중'])) {
            return back()->with('error', '경기 결과를 입력할 수 없는 상태입니다.');
        }

        return view('matches.edit-result', compact('match'));
    }

    /**
     * Update match result.
     */
    public function updateResult(Request $request, GameMatch $match)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // Only home team owner can update result
        if (!$currentTeam || $match->home_team_id !== $currentTeam->id || $currentTeam->owner_user_id !== $user->id) {
            return back()->with('error', '경기 결과 입력 권한이 없습니다.');
        }

        $validated = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
            'status' => 'required|in:진행중,완료,취소',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Update match with result
            $match->update([
                'home_score' => $validated['home_score'],
                'away_score' => $validated['away_score'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // If match is completed, finalize it (this will update team points)
            if ($validated['status'] === '완료') {
                $match->finalizeMatch();
            }

            DB::commit();

            return redirect()->route('matches.show', $match)
                ->with('success', '경기 결과가 성공적으로 입력되었습니다!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '경기 결과 입력 중 오류가 발생했습니다.');
        }
    }

    /**
     * Send a match invitation.
     */
    public function sendInvitation(Request $request)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        if (!$currentTeam || $currentTeam->owner_user_id !== $user->id) {
            return back()->with('error', '팀 오너만 경기 초대를 보낼 수 있습니다.');
        }

        $validated = $request->validate([
            'invited_team_id' => 'required|exists:teams,id',
            'proposed_date' => 'required|date|after:today',
            'proposed_time' => 'required',
            'proposed_venue' => 'required|string|max:255',
            'message' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        $invitedTeam = Team::findOrFail($validated['invited_team_id']);

        // Check if same team
        if ($invitedTeam->id === $currentTeam->id) {
            return back()->with('error', '자신의 팀에게는 경기를 초대할 수 없습니다.');
        }

        // Check if same sport
        if ($invitedTeam->sport !== $currentTeam->sport) {
            return back()->with('error', '같은 스포츠 종목의 팀에게만 경기를 초대할 수 있습니다.');
        }

        // Check if already sent invitation
        $existingInvitation = MatchInvitation::where('inviting_team_id', $currentTeam->id)
            ->where('invited_team_id', $invitedTeam->id)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return back()->with('error', '이미 이 팀에게 경기 초대를 보냈습니다.');
        }

        MatchInvitation::create([
            'inviting_team_id' => $currentTeam->id,
            'invited_team_id' => $invitedTeam->id,
            'proposed_date' => $validated['proposed_date'],
            'proposed_time' => $validated['proposed_time'],
            'proposed_venue' => $validated['proposed_venue'],
            'message' => $validated['message'],
            'contact_phone' => $validated['contact_phone'] ?? $user->phone,
            'status' => 'pending',
        ]);

        return back()->with('success', '경기 초대를 보냈습니다!');
    }

    /**
     * Accept a match invitation.
     */
    public function acceptInvitation(MatchInvitation $invitation)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        if (!$currentTeam || $invitation->invited_team_id !== $currentTeam->id || $currentTeam->owner_user_id !== $user->id) {
            return back()->with('error', '권한이 없습니다.');
        }

        if ($invitation->status !== 'pending') {
            return back()->with('error', '이미 처리된 초대입니다.');
        }

        try {
            DB::beginTransaction();

            // Accept the invitation
            $invitation->update(['status' => 'accepted']);

            // Create the match
            GameMatch::create([
                'sport' => $currentTeam->sport,
                'city' => $invitation->invitingTeam->city,
                'district' => $invitation->invitingTeam->district,
                'home_team_id' => $invitation->inviting_team_id,
                'home_team_name' => $invitation->invitingTeam->team_name,
                'away_team_id' => $currentTeam->id,
                'away_team_name' => $currentTeam->team_name,
                'match_date' => $invitation->proposed_date,
                'match_time' => $invitation->proposed_time,
                'venue' => $invitation->proposed_venue,
                'status' => '예정',
                'notes' => $invitation->message,
                'created_by' => $user->id,
            ]);

            DB::commit();

            return back()->with('success', '경기 초대를 수락했습니다! 경기가 일정에 추가되었습니다.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '경기 초대 수락 중 오류가 발생했습니다.');
        }
    }

    /**
     * Reject a match invitation.
     */
    public function rejectInvitation(MatchInvitation $invitation)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        if (!$currentTeam || $invitation->invited_team_id !== $currentTeam->id || $currentTeam->owner_user_id !== $user->id) {
            return back()->with('error', '권한이 없습니다.');
        }

        if ($invitation->status !== 'pending') {
            return back()->with('error', '이미 처리된 초대입니다.');
        }

        $invitation->update(['status' => 'rejected']);

        return back()->with('success', '경기 초대를 거절했습니다.');
    }

    /**
     * Cancel a match invitation.
     */
    public function cancelInvitation(MatchInvitation $invitation)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        if (!$currentTeam || $invitation->inviting_team_id !== $currentTeam->id || $currentTeam->owner_user_id !== $user->id) {
            return back()->with('error', '권한이 없습니다.');
        }

        if ($invitation->status !== 'pending') {
            return back()->with('error', '이미 처리된 초대입니다.');
        }

        $invitation->update(['status' => 'cancelled']);

        return back()->with('success', '경기 초대를 취소했습니다.');
    }

}
