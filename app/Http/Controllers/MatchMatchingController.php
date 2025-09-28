<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\MatchRequest;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchMatchingController extends Controller
{
    /**
     * Display match matching index.
     */
    public function index()
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // 팀이 없어도 페이지에 접근 가능
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

            // 같은 스포츠의 다른 팀들 (매칭 요청 가능한 팀들) - 지역 제한 해제
            $availableTeams = Team::where('sport', $currentTeam->sport)
                ->where('id', '!=', $currentTeam->id)
                ->with(['owner'])
                ->get();
        } else {
            // 팀이 없는 경우 모든 팀을 표시
            $availableTeams = Team::with(['owner'])->get();
        }

        return view('match-matching.index', compact(
            'currentTeam',
            'myRequests',
            'receivedRequests',
            'availableTeams'
        ));
    }

    /**
     * Store a new match request.
     */
    public function store(Request $request)
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

        try {
            MatchRequest::create([
                'requesting_team_id' => $currentTeam->id,
                'requested_team_id' => $validated['requested_team_id'],
                'match_date' => $validated['match_date'],
                'match_time' => $validated['match_time'],
                'venue' => $validated['venue'],
                'message' => $validated['message'],
                'contact_phone' => $validated['contact_phone'] ?? $user->phone,
                'status' => 'pending',
            ]);

            return back()->with('success', '매칭 요청이 전송되었습니다! 상대 팀의 응답을 기다려주세요.');

        } catch (\Exception $e) {
            return back()->with('error', '매칭 요청 중 오류가 발생했습니다.');
        }
    }

    /**
     * Accept a match request.
     */
    public function accept(Request $request, MatchRequest $matchRequest)
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
    public function reject(Request $request, MatchRequest $matchRequest)
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

        try {
            $matchRequest->update(['status' => 'rejected']);
            return back()->with('success', '매칭 요청을 거절했습니다.');

        } catch (\Exception $e) {
            return back()->with('error', '매칭 거절 중 오류가 발생했습니다.');
        }
    }

    /**
     * Cancel a match request.
     */
    public function cancel(Request $request, MatchRequest $matchRequest)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // 요청한 팀의 오너만 취소 가능
        if (!$currentTeam || $matchRequest->requesting_team_id !== $currentTeam->id || $currentTeam->owner_user_id !== $user->id) {
            return back()->with('error', '권한이 없습니다.');
        }

        if ($matchRequest->status !== 'pending') {
            return back()->with('error', '이미 처리된 요청은 취소할 수 없습니다.');
        }

        try {
            $matchRequest->update(['status' => 'cancelled']);
            return back()->with('success', '매칭 요청을 취소했습니다.');

        } catch (\Exception $e) {
            return back()->with('error', '매칭 취소 중 오류가 발생했습니다.');
        }
    }
}
