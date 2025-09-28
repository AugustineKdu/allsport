<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\Admin\RegionController as AdminRegionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('home') : view('welcome');
});


// Public API endpoints (no auth required)
Route::get('/api/regions/{city}/districts', [OnboardingController::class, 'getDistricts']);
Route::get('/api/teams/regions/{city}/districts', [TeamController::class, 'getDistricts']);

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Onboarding routes
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    // Main app routes (require onboarding completion)
    Route::middleware(['App\Http\Middleware\EnsureOnboardingCompleted'])->group(function () {
        // Home
        Route::get('/home', function () {
            return view('home');
        })->name('home');

        // Teams
        Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
        Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
        Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
        Route::get('/teams/{slug}', [TeamController::class, 'show'])->name('teams.show');
        Route::post('/teams/{slug}/apply', [TeamController::class, 'apply'])->name('teams.apply');
        Route::post('/teams/{slug}/leave', [TeamController::class, 'leave'])->name('teams.leave');
        Route::get('/teams/{slug}/manage', [TeamController::class, 'manage'])->name('teams.manage');
        Route::post('/teams/{slug}/approve/{member}', [TeamController::class, 'approve'])->name('teams.approve');
        Route::post('/teams/{slug}/reject/{member}', [TeamController::class, 'reject'])->name('teams.reject');
        Route::post('/teams/{slug}/kick/{member}', [TeamController::class, 'kick'])->name('teams.kick');

        // Matches (통합된 매칭 기능 포함)
        Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
        Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');
        Route::post('/matches/{match}/apply', [MatchController::class, 'apply'])->name('matches.apply');
        Route::get('/matches/{match}/edit-result', [MatchController::class, 'editResult'])->name('matches.edit-result');
        Route::patch('/matches/{match}/result', [MatchController::class, 'updateResult'])->name('matches.update-result');

        // Match Request routes (통합된 매칭 기능)
        Route::post('/matches/store-match-request', [MatchController::class, 'storeMatchRequest'])->name('matches.store-match-request');
        Route::post('/matches/accept-match-request/{matchRequest}', [MatchController::class, 'acceptMatchRequest'])->name('matches.accept-match-request');
        Route::post('/matches/reject-match-request/{matchRequest}', [MatchController::class, 'rejectMatchRequest'])->name('matches.reject-match-request');
        Route::post('/matches/cancel-match-request/{matchRequest}', [MatchController::class, 'cancelMatchRequest'])->name('matches.cancel-match-request');

        // Region API routes
        Route::get('/api/regions/{city}/districts', function ($city) {
            return App\Models\Region::where('city', $city)
                ->where('is_active', true)
                ->orderBy('district')
                ->pluck('district');
        });

        Route::get('/api/teams/regions/{city}/districts', function ($city) {
            return App\Models\Region::where('city', $city)
                ->where('is_active', true)
                ->orderBy('district')
                ->pluck('district');
        });


        // Rankings
        Route::get('/rankings', [RankingController::class, 'index'])->name('rankings.index');

        // My Page (Profile)
        Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Admin routes (admin only)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('regions', AdminRegionController::class);
            Route::patch('regions/{region}/toggle', [AdminRegionController::class, 'toggleActive'])->name('regions.toggle');
        });

        // Legacy dashboard redirect
        Route::get('/dashboard', function () {
            return redirect()->route('home');
        })->name('dashboard');
    });
});

// CSRF token refresh route
Route::get('/csrf-token', function (Request $request) {
    return response()->json([
        'csrf_token' => csrf_token()
    ]);
})->name('csrf.token');

require __DIR__.'/auth.php';
