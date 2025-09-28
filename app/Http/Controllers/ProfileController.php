<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Region;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $currentTeam = $user->currentTeam();
        $teamMembership = null;

        if ($currentTeam) {
            $teamMembership = $user->teamMemberships()
                ->where('team_id', $currentTeam->id)
                ->where('status', 'approved')
                ->first();
        }

        return view('mypage', [
            'user' => $user,
            'currentTeam' => $currentTeam,
            'teamMembership' => $teamMembership,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Get all regions for selection
        $regions = Region::active()
            ->orderBy('city')
            ->orderBy('district')
            ->get();

        // Get sports for sport selection
        $sports = Sport::active()->get();

        return view('profile.edit', [
            'user' => $user,
            'regions' => $regions,
            'sports' => $sports,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:20|unique:users,nickname,' . $request->user()->id,
            'phone' => 'required|string|max:20',
            'district' => 'required|string|exists:regions,district',
            'selected_sport' => 'nullable|string|exists:sports,sport_name',
        ]);

        // Find the selected region to get the city
        $region = Region::where('district', $validated['district'])
            ->where('is_active', true)
            ->first();

        if (!$region) {
            return back()->withErrors(['district' => '선택하신 지역이 유효하지 않습니다.']);
        }

        $user = $request->user();
        $user->update([
            'nickname' => $validated['nickname'],
            'phone' => $validated['phone'],
            'city' => $region->city,
            'district' => $validated['district'],
            'selected_sport' => $validated['selected_sport'],
            'onboarding_done' => true, // 프로필 설정 완료로 표시
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
