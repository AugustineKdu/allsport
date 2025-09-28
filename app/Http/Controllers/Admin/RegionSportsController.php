<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Sport;
use App\Services\DualStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegionSportsController extends Controller
{
    protected $dualStorageService;

    public function __construct(DualStorageService $dualStorageService)
    {
        $this->dualStorageService = $dualStorageService;
    }

    /**
     * 지역 관리 페이지
     */
    public function regions()
    {
        $regions = Region::orderBy('city')->orderBy('district')->paginate(50);
        $cities = Region::select('city')->distinct()->orderBy('city')->pluck('city');

        return view('admin.regions.index', compact('regions', 'cities'));
    }

    /**
     * 지역 추가
     */
    public function addRegion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => 'required|string|max:50',
            'district' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        // 중복 확인
        $exists = Region::where('city', $request->city)
            ->where('district', $request->district)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => '이미 존재하는 지역입니다.'
            ]);
        }

        try {
            $region = $this->dualStorageService->save(Region::class, [
                'city' => $request->city,
                'district' => $request->district,
                'is_active' => true,
            ], "region_{$request->city}_{$request->district}");

            return response()->json([
                'success' => true,
                'message' => '지역이 추가되었습니다.',
                'region' => $region
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '지역 추가 실패: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 지역 상태 토글
     */
    public function toggleRegion(Request $request, Region $region)
    {
        try {
            $this->dualStorageService->update(Region::class, $region->id, [
                'is_active' => !$region->is_active
            ], "region_{$region->id}");

            $status = $region->is_active ? '비활성화' : '활성화';

            return response()->json([
                'success' => true,
                'message' => "지역이 {$status}되었습니다.",
                'is_active' => !$region->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '지역 상태 변경 실패: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 지역 삭제
     */
    public function deleteRegion(Region $region)
    {
        // 해당 지역에 팀이나 경기가 있는지 확인
        $teamCount = \App\Models\Team::where('city', $region->city)
            ->where('district', $region->district)
            ->count();

        $matchCount = \App\Models\GameMatch::where('city', $region->city)
            ->where('district', $region->district)
            ->count();

        if ($teamCount > 0 || $matchCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "해당 지역에 팀({$teamCount}개) 또는 경기({$matchCount}개)가 있어 삭제할 수 없습니다."
            ]);
        }

        try {
            $this->dualStorageService->delete(Region::class, $region->id, "region_{$region->id}");

            return response()->json([
                'success' => true,
                'message' => '지역이 삭제되었습니다.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '지역 삭제 실패: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 스포츠 관리 페이지
     */
    public function sports()
    {
        $sports = Sport::orderBy('sport_name')->get();

        return view('admin.sports.index', compact('sports'));
    }

    /**
     * 스포츠 추가
     */
    public function addSport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sport_name' => 'required|string|max:50|unique:sports,sport_name',
            'icon' => 'required|string|max:10',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $sport = $this->dualStorageService->save(Sport::class, [
                'sport_name' => $request->sport_name,
                'icon' => $request->icon,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'status' => $request->is_active ? '활성' : '비활성',
            ], "sport_{$request->sport_name}");

            return response()->json([
                'success' => true,
                'message' => '스포츠가 추가되었습니다.',
                'sport' => $sport
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '스포츠 추가 실패: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 스포츠 상태 토글
     */
    public function toggleSport(Request $request, Sport $sport)
    {
        try {
            $this->dualStorageService->update(Sport::class, $sport->id, [
                'is_active' => !$sport->is_active,
                'status' => $sport->is_active ? '비활성' : '활성'
            ], "sport_{$sport->id}");

            $status = $sport->is_active ? '비활성화' : '활성화';

            return response()->json([
                'success' => true,
                'message' => "스포츠가 {$status}되었습니다.",
                'is_active' => !$sport->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '스포츠 상태 변경 실패: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 스포츠 업데이트
     */
    public function updateSport(Request $request, Sport $sport)
    {
        $validator = Validator::make($request->all(), [
            'sport_name' => 'required|string|max:50|unique:sports,sport_name,' . $sport->id,
            'icon' => 'required|string|max:10',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        try {
            $this->dualStorageService->update(Sport::class, $sport->id, [
                'sport_name' => $request->sport_name,
                'icon' => $request->icon,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'status' => $request->is_active ? '활성' : '비활성'
            ], "sport_{$sport->id}");

            return response()->json([
                'success' => true,
                'message' => '스포츠 정보가 업데이트되었습니다.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '스포츠 업데이트 실패: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 스포츠 삭제
     */
    public function deleteSport(Sport $sport)
    {
        // 해당 스포츠에 팀이나 경기가 있는지 확인
        $teamCount = \App\Models\Team::where('sport', $sport->sport_name)->count();
        $matchCount = \App\Models\GameMatch::where('sport', $sport->sport_name)->count();

        if ($teamCount > 0 || $matchCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "해당 스포츠에 팀({$teamCount}개) 또는 경기({$matchCount}개)가 있어 삭제할 수 없습니다."
            ]);
        }

        try {
            $this->dualStorageService->delete(Sport::class, $sport->id, "sport_{$sport->id}");

            return response()->json([
                'success' => true,
                'message' => '스포츠가 삭제되었습니다.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '스포츠 삭제 실패: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 대량 지역 추가
     */
    public function bulkAddRegions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'regions_data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $regionsData = json_decode($request->regions_data, true);

        if (!is_array($regionsData)) {
            return response()->json([
                'success' => false,
                'message' => '올바른 JSON 형식이 아닙니다.'
            ]);
        }

        $addedCount = 0;
        $skippedCount = 0;

        foreach ($regionsData as $regionData) {
            if (!isset($regionData['city']) || !isset($regionData['district'])) {
                $skippedCount++;
                continue;
            }

            $exists = Region::where('city', $regionData['city'])
                ->where('district', $regionData['district'])
                ->exists();

            if (!$exists) {
                try {
                    $this->dualStorageService->save(Region::class, [
                        'city' => $regionData['city'],
                        'district' => $regionData['district'],
                        'is_active' => true,
                    ], "region_{$regionData['city']}_{$regionData['district']}");

                    $addedCount++;
                } catch (\Exception $e) {
                    $skippedCount++;
                }
            } else {
                $skippedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "대량 추가 완료: {$addedCount}개 추가, {$skippedCount}개 건너뜀"
        ]);
    }
}
