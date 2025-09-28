<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
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

        // Get available teams for match creation
        $availableTeams = collect();
        if ($currentTeam) {
            $availableTeams = Team::where('sport', $currentTeam->sport)
                ->where('id', '!=', $currentTeam->id)
                ->with(['owner'])
                ->get();
        }

        // Get filter options
        $statusOptions = ['예정', '진행중', '완료', '취소'];
        $cityOptions = GameMatch::distinct()->pluck('city')->filter()->sort()->values();
        $sportOptions = GameMatch::distinct()->pluck('sport')->filter()->sort()->values();

        return view('matches.index', compact('matches', 'currentTeam', 'availableTeams', 'statusOptions', 'cityOptions', 'sportOptions'));
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

}
