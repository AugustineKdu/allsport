<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\Region;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdditionalTeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 다른 지역들 생성
            $regions = [
                ['city' => '부산광역시', 'district' => '해운대구'],
                ['city' => '대구광역시', 'district' => '수성구'],
                ['city' => '인천광역시', 'district' => '연수구'],
                ['city' => '광주광역시', 'district' => '북구'],
                ['city' => '대전광역시', 'district' => '유성구'],
            ];

            foreach ($regions as $region) {
                Region::firstOrCreate([
                    'city' => $region['city'],
                    'district' => $region['district']
                ], [
                    'is_active' => true
                ]);
            }

            // 추가 팀들 생성 (같은 스포츠 - 축구)
            $additionalTeams = [
                [
                    'name' => '해운대 타이거즈',
                    'email' => 'haeundae@allsports.com',
                    'city' => '부산광역시',
                    'district' => '해운대구',
                    'team_name' => '해운대 타이거즈',
                    'slug' => 'haeundae-tigers'
                ],
                [
                    'name' => '수성 스파르탄즈',
                    'email' => 'suseong@allsports.com',
                    'city' => '대구광역시',
                    'district' => '수성구',
                    'team_name' => '수성 스파르탄즈',
                    'slug' => 'suseong-spartans'
                ],
                [
                    'name' => '연수 이글즈',
                    'email' => 'yeonsu@allsports.com',
                    'city' => '인천광역시',
                    'district' => '연수구',
                    'team_name' => '연수 이글즈',
                    'slug' => 'yeonsu-eagles'
                ]
            ];

            foreach ($additionalTeams as $teamData) {
                // 기존 계정이 있는지 확인
                $existingUser = User::where('email', $teamData['email'])->first();
                if ($existingUser) {
                    continue; // 이미 존재하면 스킵
                }

                // 팀 오너 사용자 생성
                $teamOwner = User::create([
                    'name' => $teamData['name'],
                    'email' => $teamData['email'],
                    'password' => Hash::make('password'),
                    'nickname' => $teamData['name'],
                    'city' => $teamData['city'],
                    'district' => $teamData['district'],
                    'selected_sport' => '축구',
                    'onboarding_done' => true,
                    'role' => 'user',
                ]);

                // 팀 생성
                $team = Team::create([
                    'team_name' => $teamData['team_name'],
                    'sport' => '축구',
                    'city' => $teamData['city'],
                    'district' => $teamData['district'],
                    'owner_user_id' => $teamOwner->id,
                    'slug' => $teamData['slug'],
                ]);

                // 팀 오너를 팀 멤버로 추가
                DB::table('team_members')->insert([
                    'team_id' => $team->id,
                    'user_id' => $teamOwner->id,
                    'role' => 'owner',
                    'status' => 'approved',
                    'joined_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->command->info('추가 팀들이 성공적으로 생성되었습니다!');
        });
    }
}
