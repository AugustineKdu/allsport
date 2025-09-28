<?php

namespace App\Helpers;

class RegionHelper
{
    /**
     * 표준 지역명과 DB 저장명 매핑
     */
    private static $regionMapping = [
        // 표준명 => DB저장명
        '서울특별시' => '서울',
        '서울시' => '서울',
        '서울' => '서울',

        '인천광역시' => '인천',
        '인천시' => '인천',
        '인천' => '인천',

        '경기도' => '경기',
        '경기' => '경기',
    ];

    /**
     * 표준 지역명 목록 (사용자에게 보여질 이름들)
     */
    private static $standardRegionNames = [
        '서울',
        '인천',
        '경기',
    ];

    /**
     * 표준 지역명을 DB 저장명으로 변환
     */
    public static function standardToDatabase($standardName)
    {
        return self::$regionMapping[$standardName] ?? $standardName;
    }

    /**
     * DB 저장명을 표준 지역명으로 변환
     */
    public static function databaseToStandard($dbName)
    {
        $flipped = array_flip(self::$regionMapping);

        // 표준명들 중에서 해당 DB명에 해당하는 것 찾기
        foreach (self::$standardRegionNames as $standardName) {
            if (self::$regionMapping[$standardName] === $dbName) {
                return $standardName;
            }
        }

        return $dbName;
    }

    /**
     * 표준 지역명 목록 반환
     */
    public static function getStandardRegionNames()
    {
        return self::$standardRegionNames;
    }

    /**
     * 지역명이 유효한지 확인
     */
    public static function isValidRegion($regionName)
    {
        return array_key_exists($regionName, self::$regionMapping);
    }
}