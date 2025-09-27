<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, '관리자만 접근 가능합니다.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $regions = Region::orderBy('city')
            ->orderBy('district')
            ->paginate(20);

        return view('admin.regions.index', compact('regions'));
    }

    public function create()
    {
        return view('admin.regions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
        ]);

        try {
            Region::create([
                'city' => $request->city,
                'district' => $request->district,
                'is_active' => true,
            ]);

            return redirect()->route('admin.regions.index')
                ->with('success', '지역이 성공적으로 추가되었습니다.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => '이미 존재하는 지역입니다.'])
                ->withInput();
        }
    }

    public function edit(Region $region)
    {
        return view('admin.regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $request->validate([
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        try {
            $region->update([
                'city' => $request->city,
                'district' => $request->district,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.regions.index')
                ->with('success', '지역이 성공적으로 수정되었습니다.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => '수정에 실패했습니다.'])
                ->withInput();
        }
    }

    public function destroy(Region $region)
    {
        try {
            // 해당 지역에 팀이나 사용자가 있는지 확인
            $hasUsers = \App\Models\User::where('city', $region->city)
                ->where('district', $region->district)
                ->exists();

            $hasTeams = \App\Models\Team::where('city', $region->city)
                ->where('district', $region->district)
                ->exists();

            if ($hasUsers || $hasTeams) {
                return back()->withErrors(['error' => '해당 지역에 사용자나 팀이 있어 삭제할 수 없습니다. 비활성화를 권장합니다.']);
            }

            $region->delete();

            return redirect()->route('admin.regions.index')
                ->with('success', '지역이 성공적으로 삭제되었습니다.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => '삭제에 실패했습니다.']);
        }
    }

    public function toggleActive(Region $region)
    {
        $region->update(['is_active' => !$region->is_active]);

        $status = $region->is_active ? '활성화' : '비활성화';
        return back()->with('success', "지역이 {$status}되었습니다.");
    }
}