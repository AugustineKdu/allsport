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

        '부산광역시' => '부산',
        '부산시' => '부산',
        '부산' => '부산',

        '대구광역시' => '대구',
        '대구시' => '대구',
        '대구' => '대구',

        '인천광역시' => '인천',
        '인천시' => '인천',
        '인천' => '인천',

        '광주광역시' => '광주',
        '광주시' => '광주',
        '광주' => '광주',

        '대전광역시' => '대전',
        '대전시' => '대전',
        '대전' => '대전',

        '울산광역시' => '울산',
        '울산시' => '울산',
        '울산' => '울산',

        '경기도' => '경기',
        '경기' => '경기',

        '강원도' => '강원',
        '강원' => '강원',

        '충청북도' => '충북',
        '충북' => '충북',

        '충청남도' => '충남',
        '충남' => '충남',

        '전라북도' => '전북',
        '전북' => '전북',

        '전라남도' => '전남',
        '전남' => '전남',

        '경상북도' => '경북',
        '경북' => '경북',

        '경상남도' => '경남',
        '경남' => '경남',

        '제주특별자치도' => '제주',
        '제주도' => '제주',
        '제주' => '제주',

        '세종특별자치시' => '세종',
        '세종시' => '세종',
        '세종' => '세종',
    ];

    /**
     * 표준 지역명 목록 (사용자에게 보여질 이름들)
     */
    private static $standardRegionNames = [
        '서울특별시',
        '부산광역시',
        '대구광역시',
        '인천광역시',
        '광주광역시',
        '대전광역시',
        '울산광역시',
        '세종특별자치시',
        '경기도',
        '강원도',
        '충청북도',
        '충청남도',
        '전라북도',
        '전라남도',
        '경상북도',
        '경상남도',
        '제주특별자치도',
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