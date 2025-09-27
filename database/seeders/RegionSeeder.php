<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            // 서울특별시
            ['city' => '서울', 'district' => '송파구'],
            ['city' => '서울', 'district' => '강남구'],
            ['city' => '서울', 'district' => '강동구'],
            ['city' => '서울', 'district' => '강북구'],
            ['city' => '서울', 'district' => '강서구'],
            ['city' => '서울', 'district' => '관악구'],
            ['city' => '서울', 'district' => '광진구'],
            ['city' => '서울', 'district' => '구로구'],
            ['city' => '서울', 'district' => '금천구'],
            ['city' => '서울', 'district' => '노원구'],
            ['city' => '서울', 'district' => '도봉구'],
            ['city' => '서울', 'district' => '동대문구'],
            ['city' => '서울', 'district' => '동작구'],
            ['city' => '서울', 'district' => '마포구'],
            ['city' => '서울', 'district' => '서대문구'],
            ['city' => '서울', 'district' => '서초구'],
            ['city' => '서울', 'district' => '성동구'],
            ['city' => '서울', 'district' => '성북구'],
            ['city' => '서울', 'district' => '양천구'],
            ['city' => '서울', 'district' => '영등포구'],
            ['city' => '서울', 'district' => '용산구'],
            ['city' => '서울', 'district' => '은평구'],
            ['city' => '서울', 'district' => '종로구'],
            ['city' => '서울', 'district' => '중구'],
            ['city' => '서울', 'district' => '중랑구'],

            // 경기도
            ['city' => '경기', 'district' => '수원시'],
            ['city' => '경기', 'district' => '성남시'],
            ['city' => '경기', 'district' => '의정부시'],
            ['city' => '경기', 'district' => '안양시'],
            ['city' => '경기', 'district' => '부천시'],
            ['city' => '경기', 'district' => '광명시'],
            ['city' => '경기', 'district' => '평택시'],
            ['city' => '경기', 'district' => '과천시'],
            ['city' => '경기', 'district' => '오산시'],
            ['city' => '경기', 'district' => '시흥시'],
            ['city' => '경기', 'district' => '군포시'],
            ['city' => '경기', 'district' => '의왕시'],
            ['city' => '경기', 'district' => '하남시'],
            ['city' => '경기', 'district' => '용인시'],
            ['city' => '경기', 'district' => '파주시'],
            ['city' => '경기', 'district' => '이천시'],
            ['city' => '경기', 'district' => '안성시'],
            ['city' => '경기', 'district' => '김포시'],
            ['city' => '경기', 'district' => '화성시'],
            ['city' => '경기', 'district' => '광주시'],
            ['city' => '경기', 'district' => '여주시'],

            // 인천광역시
            ['city' => '인천', 'district' => '중구'],
            ['city' => '인천', 'district' => '동구'],
            ['city' => '인천', 'district' => '미추홀구'],
            ['city' => '인천', 'district' => '연수구'],
            ['city' => '인천', 'district' => '남동구'],
            ['city' => '인천', 'district' => '부평구'],
            ['city' => '인천', 'district' => '계양구'],
            ['city' => '인천', 'district' => '서구'],
            ['city' => '인천', 'district' => '강화군'],
            ['city' => '인천', 'district' => '옹진군'],
        ];

        foreach ($regions as $region) {
            Region::firstOrCreate(
                ['city' => $region['city'], 'district' => $region['district']],
                $region
            );
        }
    }
}
