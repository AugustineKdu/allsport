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

        // Get cities for selection
        $cities = Region::active()
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

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
            'phone' => 'required|string|max:20',
        ]);

        // Verify city and district combination
        $region = Region::where('city', $validated['city'])
            ->where('district', $validated['district'])
            ->where('is_active', true)
            ->first();

        if (!$region) {
            return back()->withErrors(['district' => '선택하신 지역이 유효하지 않습니다.']);
        }

        $user = Auth::user();
        $user->update([
            'nickname' => $validated['nickname'],
            'city' => $validated['city'],
            'district' => $validated['district'],
            'selected_sport' => $validated['selected_sport'],
            'phone' => $validated['phone'],
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
