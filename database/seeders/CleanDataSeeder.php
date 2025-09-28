<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\GameMatch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CleanDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 기존 더미 데이터 삭제
        $this->command->info('🧹 기존 더미 데이터 삭제 중...');

        // 경기 삭제
        GameMatch::truncate();
        $this->command->info('✅ 경기 데이터 삭제 완료');

        // 팀 멤버십 삭제
        TeamMember::truncate();
        $this->command->info('✅ 팀 멤버십 데이터 삭제 완료');

        // 팀 삭제
        Team::truncate();
        $this->command->info('✅ 팀 데이터 삭제 완료');

        // 더미 사용자 삭제 (관리자 계정과 기본 테스트 계정은 유지)
        $dummyEmails = [
            'kim@example.com', 'lee@example.com', 'park@example.com', 'choi@example.com',
            'jung@example.com', 'han@example.com', 'yoon@example.com', 'lim@example.com',
            'kang@example.com', 'seo@example.com', 'jo@example.com', 'bae@example.com',
            'hong@example.com', 'young@example.com', 'min@example.com'
        ];

        User::whereIn('email', $dummyEmails)->delete();
        $this->command->info('✅ 더미 사용자 데이터 삭제 완료');

        // 간단한 테스트 팀 생성
        $this->command->info('🏆 테스트 팀 생성 중...');

        // 팀 오너가 팀을 생성
        $teamOwner = User::where('email', 'teamowner@allsports.com')->first();
        if ($teamOwner) {
            $team = Team::create([
                'team_name' => '테스트 팀 FC',
                'sport' => '축구',
                'city' => '서울특별시',
                'district' => '강남구',
                'owner_user_id' => $teamOwner->id,
                'wins' => 3,
                'draws' => 1,
                'losses' => 1,
                'points' => 10,
            ]);

            // 팀 오너를 팀 멤버로 추가
            TeamMember::create([
                'team_id' => $team->id,
                'user_id' => $teamOwner->id,
                'role' => 'owner',
                'status' => 'approved',
                'joined_at' => now(),
            ]);

            // 일반 사용자를 팀 멤버로 추가
            $regularUser = User::where('email', 'test@allsports.com')->first();
            if ($regularUser) {
                TeamMember::create([
                    'team_id' => $team->id,
                    'user_id' => $regularUser->id,
                    'role' => 'member',
                    'status' => 'approved',
                    'joined_at' => now(),
                ]);
            }

            $this->command->info('✅ 테스트 팀 생성 완료: ' . $team->team_name);
        }

        // 간단한 테스트 경기 생성
        $this->command->info('⚽ 테스트 경기 생성 중...');

        $testTeam = Team::first();
        if ($testTeam) {
            // 홈 경기 생성 (상대팀 없음)
            GameMatch::create([
                'sport' => '축구',
                'city' => '서울특별시',
                'district' => '강남구',
                'home_team_id' => $testTeam->id,
                'home_team_name' => $testTeam->team_name,
                'match_date' => now()->addDays(3),
                'match_time' => '14:00:00',
                'status' => '예정',
                'created_by' => $teamOwner->id,
            ]);

            $this->command->info('✅ 테스트 경기 생성 완료');
        }

        $this->command->info('');
        $this->command->info('🎉 데이터 정리 완료!');
        $this->command->info('📊 현재 데이터:');
        $this->command->info('   - 사용자: ' . User::count() . '명');
        $this->command->info('   - 팀: ' . Team::count() . '개');
        $this->command->info('   - 팀 멤버십: ' . TeamMember::count() . '개');
        $this->command->info('   - 경기: ' . GameMatch::count() . '개');
        $this->command->info('');
        $this->command->info('🔐 사용 가능한 계정:');
        $this->command->info('   - developer@allsports.com / password (관리자)');
        $this->command->info('   - owner@allsports.com / password (관리자)');
        $this->command->info('   - teamowner@allsports.com / password (팀 오너)');
        $this->command->info('   - test@allsports.com / password (일반 사용자)');
    }
}
