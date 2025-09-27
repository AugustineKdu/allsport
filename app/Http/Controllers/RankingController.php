<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RankingController extends Controller
{
    /**
     * Display rankings.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $scope = $request->input('scope', 'district'); // default to district
        $sport = $request->input('sport', $user->selected_sport);
        $city = $request->input('city', $user->city);
        $district = $request->input('district', $user->district);

        $query = Team::where('sport', $sport);

        switch ($scope) {
            case 'all':
                // National ranking - no additional filters
                break;

            case 'city':
                $query->where('city', $city);
                break;

            case 'district':
            default:
                $query->where('city', $city)
                      ->where('district', $district);
                break;
        }

        $teams = $query->orderBy('points', 'desc')
                       ->orderBy('wins', 'desc')
                       ->paginate(20);

        // Get top 5 teams in parent city if viewing district
        $cityTopTeams = null;
        if ($scope === 'district') {
            $cityTopTeams = Team::where('sport', $sport)
                ->where('city', $city)
                ->orderBy('points', 'desc')
                ->orderBy('wins', 'desc')
                ->limit(5)
                ->get();
        }

        return view('rankings.index', compact('teams', 'scope', 'sport', 'city', 'district', 'cityTopTeams'));
    }
}
