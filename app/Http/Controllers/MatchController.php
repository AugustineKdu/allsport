<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\MatchRequest;
use App\Models\Team;
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

        // Get matching data for the current team
        $myRequests = collect();
        $receivedRequests = collect();
        $availableTeams = collect();

        if ($currentTeam) {
            // 내 팀이 생성한 매칭 요청들 (아직 수락되지 않은 것들)
            $myRequests = MatchRequest::where('requesting_team_id', $currentTeam->id)
                ->where('status', 'pending')
                ->with(['requestedTeam'])
                ->orderBy('created_at', 'desc')
                ->get();

            // 내 팀에게 온 매칭 요청들
            $receivedRequests = MatchRequest::where('requested_team_id', $currentTeam->id)
                ->where('status', 'pending')
                ->with(['requestingTeam'])
                ->orderBy('created_at', 'desc')
                ->get();

            // 같은 스포츠의 다른 팀들 (매칭 요청 가능한 팀들)
            $availableTeams = Team::where('sport', $currentTeam->sport)
                ->where('id', '!=', $currentTeam->id)
                ->with(['owner'])
                ->get();
        } else {
            // 팀이 없는 경우 모든 팀을 표시
            $availableTeams = Team::with(['owner'])->get();
        }

        return view('matches.index', compact('matches', 'currentTeam', 'myRequests', 'receivedRequests', 'availableTeams'));
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
     * Store a new match request.
     */
    public function storeMatchRequest(Request $request)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // 팀 오너만 매칭 요청 가능
        if (!$currentTeam || $currentTeam->owner_user_id !== $user->id) {
            return back()->with('error', '팀 오너만 매칭을 요청할 수 있습니다.');
        }

        $validated = $request->validate([
            'requested_team_id' => 'required|exists:teams,id',
            'match_date' => 'required|date|after:today',
            'match_time' => 'required|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        // 같은 팀에게 요청하는지 확인
        if ($validated['requested_team_id'] == $currentTeam->id) {
            return back()->with('error', '자신의 팀에게는 매칭을 요청할 수 없습니다.');
        }

        // 요청받는 팀이 같은 스포츠인지 확인
        $requestedTeam = Team::findOrFail($validated['requested_team_id']);
        if ($requestedTeam->sport !== $currentTeam->sport) {
            return back()->with('error', '같은 스포츠 종목의 팀에게만 매칭을 요청할 수 있습니다.');
        }

        // 이미 같은 팀에게 요청했는지 확인
        $existingRequest = MatchRequest::where('requesting_team_id', $currentTeam->id)
            ->where('requested_team_id', $validated['requested_team_id'])
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('error', '이미 이 팀에게 매칭을 요청했습니다.');
        }

        MatchRequest::create([
            'requesting_team_id' => $currentTeam->id,
            'requested_team_id' => $validated['requested_team_id'],
            'match_date' => $validated['match_date'],
            'match_time' => $validated['match_time'],
            'venue' => $validated['venue'],
            'message' => $validated['message'],
            'contact_phone' => $validated['contact_phone'],
            'status' => 'pending',
        ]);

        return back()->with('success', '매칭 요청을 보냈습니다!');
    }

    /**
     * Accept a match request.
     */
    public function acceptMatchRequest(MatchRequest $matchRequest)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // 요청받는 팀의 오너만 수락 가능
        if (!$currentTeam || $matchRequest->requested_team_id !== $currentTeam->id || $currentTeam->owner_user_id !== $user->id) {
            return back()->with('error', '권한이 없습니다.');
        }

        if ($matchRequest->status !== 'pending') {
            return back()->with('error', '이미 처리된 요청입니다.');
        }

        try {
            // 매칭 요청을 수락
            $matchRequest->update(['status' => 'accepted']);

            // 실제 경기 생성
            GameMatch::create([
                'sport' => $currentTeam->sport,
                'city' => $currentTeam->city,
                'district' => $currentTeam->district,
                'home_team_id' => $currentTeam->id,
                'home_team_name' => $currentTeam->team_name,
                'away_team_id' => $matchRequest->requestingTeam->id,
                'away_team_name' => $matchRequest->requestingTeam->team_name,
                'match_date' => $matchRequest->match_date,
                'match_time' => $matchRequest->match_time,
                'status' => '예정',
                'created_by' => $user->id,
            ]);

            return back()->with('success', '매칭이 수락되었습니다! 경기가 일정에 추가되었습니다.');

        } catch (\Exception $e) {
            return back()->with('error', '매칭 수락 중 오류가 발생했습니다.');
        }
    }

    /**
     * Reject a match request.
     */
    public function rejectMatchRequest(MatchRequest $matchRequest)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // 요청받는 팀의 오너만 거절 가능
        if (!$currentTeam || $matchRequest->requested_team_id !== $currentTeam->id || $currentTeam->owner_user_id !== $user->id) {
            return back()->with('error', '권한이 없습니다.');
        }

        if ($matchRequest->status !== 'pending') {
            return back()->with('error', '이미 처리된 요청입니다.');
        }

        $matchRequest->update(['status' => 'rejected']);

        return back()->with('success', '매칭 요청을 거절했습니다.');
    }

    /**
     * Cancel a match request.
     */
    public function cancelMatchRequest(MatchRequest $matchRequest)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // 요청한 팀의 오너만 취소 가능
        if (!$currentTeam || $matchRequest->requesting_team_id !== $currentTeam->id || $currentTeam->owner_user_id !== $user->id) {
            return back()->with('error', '권한이 없습니다.');
        }

        if ($matchRequest->status !== 'pending') {
            return back()->with('error', '이미 처리된 요청입니다.');
        }

        $matchRequest->update(['status' => 'cancelled']);

        return back()->with('success', '매칭 요청을 취소했습니다.');
    }
}
