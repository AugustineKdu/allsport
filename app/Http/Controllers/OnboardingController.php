<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Sport;
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
            'city' => 'required|string|exists:regions,city',
            'district' => 'required|string|exists:regions,district',
            'selected_sport' => 'required|string|exists:sports,sport_name',
        ]);

        // Verify city and district combination exists
        $regionExists = Region::where('city', $validated['city'])
            ->where('district', $validated['district'])
            ->where('is_active', true)
            ->exists();

        if (!$regionExists) {
            return back()->withErrors(['district' => '선택하신 지역이 유효하지 않습니다.']);
        }

        $user = Auth::user();
        $user->update([
            'nickname' => $validated['nickname'],
            'city' => $validated['city'],
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
        $districts = Region::active()
            ->where('city', $city)
            ->orderBy('district')
            ->pluck('district');

        return response()->json($districts);
    }
}
