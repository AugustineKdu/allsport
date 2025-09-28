<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Sport;
use App\Helpers\RegionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding form.
     */
    public function show()
    {
        $user = Auth::user();

        // If user already completed onboarding, redirect to dashboard
        if ($user->onboarding_done) {
            return redirect()->route('dashboard');
        }

        // Get cities from database and convert to standard names for display
        $dbCities = Region::active()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        // Convert to standard region names for display
        $cities = $dbCities->map(function($city) {
            return RegionHelper::databaseToStandard($city);
        })->unique()->sort()->values();

        $sports = Sport::active()->get();

        return view('onboarding.index', compact('cities', 'sports'));
    }

    /**
     * Process the onboarding form submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:20|unique:users,nickname',
            'city' => 'required|string',
            'district' => 'required|string',
            'selected_sport' => 'required|string|exists:sports,sport_name',
        ]);

        // Convert standard region name to database name
        $dbCity = RegionHelper::standardToDatabase($validated['city']);

        // Verify city and district combination exists
        $regionExists = Region::where('city', $dbCity)
            ->where('district', $validated['district'])
            ->where('is_active', true)
            ->exists();

        if (!$regionExists) {
            return back()->withErrors(['district' => '선택하신 지역이 유효하지 않습니다.']);
        }

        $user = Auth::user();
        $user->update([
            'nickname' => $validated['nickname'],
            'city' => $dbCity, // Save database name
            'district' => $validated['district'],
            'selected_sport' => $validated['selected_sport'],
            'onboarding_done' => true,
        ]);

        return redirect()->route('teams.index')->with('success', '온보딩이 완료되었습니다! 팀을 찾아보세요.');
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
