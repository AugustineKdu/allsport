<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sport;

class SportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = [
            [
                'sport_name' => '축구',
                'icon' => '⚽',
                'is_active' => true,
                'status' => '11인 축구 - 표준 축구 경기',
            ],
            [
                'sport_name' => '풋살',
                'icon' => '⚽',
                'is_active' => true,
                'status' => '5인 축구 - 실내 미니 축구',
            ],
            [
                'sport_name' => '농구',
                'icon' => '🏀',
                'is_active' => false,
                'status' => '베타 서비스 - 추후 추가 예정',
            ],
            [
                'sport_name' => '배구',
                'icon' => '🏐',
                'is_active' => false,
                'status' => '베타 서비스 - 추후 추가 예정',
            ],
            [
                'sport_name' => '야구',
                'icon' => '⚾',
                'is_active' => false,
                'status' => '베타 서비스 - 추후 추가 예정',
            ],
            [
                'sport_name' => '탁구',
                'icon' => '🏓',
                'is_active' => false,
                'status' => '베타 서비스 - 추후 추가 예정',
            ],
            [
                'sport_name' => '배드민턴',
                'icon' => '🏸',
                'is_active' => false,
                'status' => '베타 서비스 - 추후 추가 예정',
            ],
            [
                'sport_name' => '테니스',
                'icon' => '🎾',
                'is_active' => false,
                'status' => '베타 서비스 - 추후 추가 예정',
            ],
        ];

        foreach ($sports as $sport) {
            Sport::firstOrCreate(
                ['sport_name' => $sport['sport_name']],
                $sport
            );
        }
    }
}
