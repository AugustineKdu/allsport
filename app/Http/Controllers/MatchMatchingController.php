<?php

namespace App\Http\Controllers;

use App\Models\MatchRequest;
use App\Models\Team;
use App\Models\GameMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MatchMatchingController extends Controller
{
    /**
     * Show the match matching page
     */
    public function index()
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        if (!$currentTeam) {
            return redirect()->route('teams.index')->with('error', '팀에 가입해야 경기 매칭을 이용할 수 있습니다.');
        }

        // Get teams in the same sport and region
        $availableTeams = Team::where('id', '!=', $currentTeam->id)
            ->where('sport', $currentTeam->sport)
            ->where('city', $currentTeam->city)
            ->where('district', $currentTeam->district)
            ->with('owner')
            ->get();

        // Get pending requests to this team
        $pendingRequests = MatchRequest::where('home_team_id', $currentTeam->id)
            ->where('status', 'pending')
            ->with(['requestingTeam', 'requestingTeam.owner'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get requests made by this team
        $myRequests = MatchRequest::where('requesting_team_id', $currentTeam->id)
            ->with(['homeTeam', 'homeTeam.owner'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('match-matching.index', compact('availableTeams', 'pendingRequests', 'myRequests'));
    }

    /**
     * Store a new match request
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        if (!$currentTeam) {
            return back()->withErrors(['error' => '팀에 가입해야 경기 요청을 할 수 있습니다.']);
        }

        $validated = $request->validate([
            'home_team_id' => 'required|exists:teams,id',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'required',
            'message' => 'nullable|string|max:500',
        ]);

        // Check if team is requesting to itself
        if ($validated['home_team_id'] == $currentTeam->id) {
            return back()->withErrors(['error' => '자신의 팀에게는 경기 요청을 할 수 없습니다.']);
        }

        // Check if there's already a pending request
        $existingRequest = MatchRequest::where('home_team_id', $validated['home_team_id'])
            ->where('requesting_team_id', $currentTeam->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->withErrors(['error' => '이미 이 팀에게 경기 요청을 보냈습니다.']);
        }

        $homeTeam = Team::find($validated['home_team_id']);

        // Create match request
        MatchRequest::create([
            'home_team_id' => $validated['home_team_id'],
            'requesting_team_id' => $currentTeam->id,
            'sport' => $currentTeam->sport,
            'city' => $currentTeam->city,
            'district' => $currentTeam->district,
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'],
            'message' => $validated['message'],
        ]);

        return back()->with('success', $homeTeam->team_name . ' 팀에게 경기 요청을 보냈습니다.');
    }

    /**
     * Accept a match request
     */
    public function accept(Request $request, MatchRequest $matchRequest)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // Check if user is the owner of the home team
        if (!$currentTeam || $currentTeam->id != $matchRequest->home_team_id || $currentTeam->owner_user_id != $user->id) {
            return back()->withErrors(['error' => '권한이 없습니다.']);
        }

        if ($matchRequest->status !== 'pending') {
            return back()->withErrors(['error' => '이미 처리된 요청입니다.']);
        }

        DB::transaction(function () use ($matchRequest) {
            // Update match request status
            $matchRequest->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            // Create the actual match
            GameMatch::create([
                'sport' => $matchRequest->sport,
                'city' => $matchRequest->city,
                'district' => $matchRequest->district,
                'home_team_id' => $matchRequest->home_team_id,
                'away_team_id' => $matchRequest->requesting_team_id,
                'home_team_name' => $matchRequest->homeTeam->team_name,
                'away_team_name' => $matchRequest->requestingTeam->team_name,
                'match_date' => $matchRequest->preferred_date,
                'match_time' => $matchRequest->preferred_time,
                'status' => '예정',
                'created_by' => auth()->id(),
            ]);
        });

        return back()->with('success', '경기 요청을 수락했습니다. 경기가 생성되었습니다.');
    }

    /**
     * Reject a match request
     */
    public function reject(Request $request, MatchRequest $matchRequest)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // Check if user is the owner of the home team
        if (!$currentTeam || $currentTeam->id != $matchRequest->home_team_id || $currentTeam->owner_user_id != $user->id) {
            return back()->withErrors(['error' => '권한이 없습니다.']);
        }

        if ($matchRequest->status !== 'pending') {
            return back()->withErrors(['error' => '이미 처리된 요청입니다.']);
        }

        $matchRequest->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);

        return back()->with('success', '경기 요청을 거절했습니다.');
    }

    /**
     * Cancel a match request
     */
    public function cancel(Request $request, MatchRequest $matchRequest)
    {
        $user = Auth::user();
        $currentTeam = $user->currentTeam();

        // Check if user is the owner of the requesting team
        if (!$currentTeam || $currentTeam->id != $matchRequest->requesting_team_id || $currentTeam->owner_user_id != $user->id) {
            return back()->withErrors(['error' => '권한이 없습니다.']);
        }

        if ($matchRequest->status !== 'pending') {
            return back()->withErrors(['error' => '이미 처리된 요청입니다.']);
        }

        $matchRequest->update([
            'status' => 'cancelled',
            'responded_at' => now(),
        ]);

        return back()->with('success', '경기 요청을 취소했습니다.');
    }
}
