<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\Region;
use App\Models\Sport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FixedTestAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 기존 테스트 계정들 삭제 (이메일로 식별)
        $testEmails = [
            'developer@allsports.com',
            'owner@allsports.com',
            'test@allsports.com',
            'teamowner@allsports.com'
        ];

        // 관련 데이터들을 먼저 삭제
        $usersToDelete = User::whereIn('email', $testEmails)->get();
        foreach ($usersToDelete as $user) {
            // 소유한 팀들의 관련 데이터 삭제
            $ownedTeams = Team::where('owner_user_id', $user->id)->get();
            foreach ($ownedTeams as $team) {
                // 팀과 관련된 경기들 삭제
                DB::table('matches')->where('home_team_id', $team->id)
                    ->orWhere('away_team_id', $team->id)->delete();

                // 팀 멤버십 삭제
                DB::table('team_members')->where('team_id', $team->id)->delete();

                // 팀 삭제
                $team->delete();
            }

            // 매치 요청 삭제
            DB::table('match_requests')->where('requesting_team_id', $user->id)
                ->orWhere('requested_team_id', $user->id)->delete();

            // 사용자 삭제
            $user->delete();
        }

        DB::transaction(function () {
            // 지역 데이터 확인 및 생성
            $seoulRegion = Region::firstOrCreate([
                'city' => '서울특별시',
                'district' => '송파구'
            ], [
                'is_active' => true
            ]);

            // 스포츠 데이터 확인 및 생성
            $footballSport = Sport::firstOrCreate([
                'sport_name' => '축구'
            ], [
                'is_active' => true
            ]);

            // 1. 개발자 계정
            $developer = User::create([
                'name' => '개발자',
                'email' => 'developer@allsports.com',
                'password' => Hash::make('password'),
                'nickname' => '개발자',
                'city' => '서울특별시',
                'district' => '송파구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'admin',
            ]);

            // 2. 오너 계정
            $owner = User::create([
                'name' => '오너',
                'email' => 'owner@allsports.com',
                'password' => Hash::make('password'),
                'nickname' => '오너',
                'city' => '서울특별시',
                'district' => '송파구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'admin',
            ]);

            // 3. 일반 사용자
            $testUser = User::create([
                'name' => '테스트사용자',
                'email' => 'test@allsports.com',
                'password' => Hash::make('password'),
                'nickname' => '테스트유저',
                'city' => '서울특별시',
                'district' => '송파구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'user',
            ]);

            // 4. 팀 오너
            $teamOwner = User::create([
                'name' => '팀오너',
                'email' => 'teamowner@allsports.com',
                'password' => Hash::make('password'),
                'nickname' => '팀오너',
                'city' => '서울특별시',
                'district' => '송파구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'user',
            ]);

            // 팀 오너를 위한 팀 생성
            $team = Team::create([
                'team_name' => '송파 레전드',
                'sport' => '축구',
                'city' => '서울특별시',
                'district' => '송파구',
                'owner_user_id' => $teamOwner->id,
                'slug' => 'sangpa-legend-' . time(),
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

            $this->command->info('고정 테스트 계정들이 성공적으로 생성되었습니다!');
            $this->command->info('개발자: developer@allsports.com / password');
            $this->command->info('오너: owner@allsports.com / password');
            $this->command->info('일반사용자: test@allsports.com / password');
            $this->command->info('팀오너: teamowner@allsports.com / password');
        });
    }
}
