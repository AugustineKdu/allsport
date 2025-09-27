<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
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

        return view('matches.index', compact('matches'));
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
}
