<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\GameMatch;
use App\Models\Region;
use App\Models\Sport;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 더미 사용자 생성
        $users = [
            [
                'name' => '김철수',
                'email' => 'kim@example.com',
                'nickname' => '철수',
                'city' => '서울',
                'district' => '강남구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'team_owner',
            ],
            [
                'name' => '이영희',
                'email' => 'lee@example.com',
                'nickname' => '영희',
                'city' => '서울',
                'district' => '강남구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'user',
            ],
            [
                'name' => '박민수',
                'email' => 'park@example.com',
                'nickname' => '민수',
                'city' => '서울',
                'district' => '송파구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'team_owner',
            ],
            [
                'name' => '최지영',
                'email' => 'choi@example.com',
                'nickname' => '지영',
                'city' => '서울',
                'district' => '송파구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'user',
            ],
            [
                'name' => '정현우',
                'email' => 'jung@example.com',
                'nickname' => '현우',
                'city' => '서울',
                'district' => '강동구',
                'selected_sport' => '풋살',
                'onboarding_done' => true,
                'role' => 'team_owner',
            ],
            [
                'name' => '한소영',
                'email' => 'han@example.com',
                'nickname' => '소영',
                'city' => '서울',
                'district' => '강동구',
                'selected_sport' => '풋살',
                'onboarding_done' => true,
                'role' => 'user',
            ],
            [
                'name' => '윤태호',
                'email' => 'yoon@example.com',
                'nickname' => '태호',
                'city' => '경기',
                'district' => '성남시',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'team_owner',
            ],
            [
                'name' => '임수진',
                'email' => 'lim@example.com',
                'nickname' => '수진',
                'city' => '경기',
                'district' => '성남시',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'user',
            ],
            [
                'name' => '강동현',
                'email' => 'kang@example.com',
                'nickname' => '동현',
                'city' => '경기',
                'district' => '수원시',
                'selected_sport' => '풋살',
                'onboarding_done' => true,
                'role' => 'team_owner',
            ],
            [
                'name' => '서미래',
                'email' => 'seo@example.com',
                'nickname' => '미래',
                'city' => '경기',
                'district' => '수원시',
                'selected_sport' => '풋살',
                'onboarding_done' => true,
                'role' => 'user',
            ],
            [
                'name' => '조성민',
                'email' => 'jo@example.com',
                'nickname' => '성민',
                'city' => '인천',
                'district' => '연수구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'team_owner',
            ],
            [
                'name' => '배지훈',
                'email' => 'bae@example.com',
                'nickname' => '지훈',
                'city' => '인천',
                'district' => '연수구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'user',
            ],
            [
                'name' => '홍길동',
                'email' => 'hong@example.com',
                'nickname' => '길동',
                'city' => '서울',
                'district' => '강남구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'user',
            ],
            [
                'name' => '김영수',
                'email' => 'young@example.com',
                'nickname' => '영수',
                'city' => '서울',
                'district' => '송파구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'role' => 'user',
            ],
            [
                'name' => '이민정',
                'email' => 'min@example.com',
                'nickname' => '민정',
                'city' => '서울',
                'district' => '강동구',
                'selected_sport' => '풋살',
                'onboarding_done' => true,
                'role' => 'user',
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'nickname' => $userData['nickname'],
                    'city' => $userData['city'],
                    'district' => $userData['district'],
                    'selected_sport' => $userData['selected_sport'],
                    'onboarding_done' => $userData['onboarding_done'],
                    'role' => $userData['role'],
                    'email_verified_at' => now(),
                ]
            );
            $createdUsers[] = $user;
        }

        // 더미 팀 생성
        $teams = [
            [
                'team_name' => '강남 유나이티드 FC',
                'sport' => '축구',
                'city' => '서울',
                'district' => '강남구',
                'owner_user_id' => $createdUsers[0]->id, // 김철수
                'wins' => 5,
                'draws' => 2,
                'losses' => 1,
                'points' => 17,
            ],
            [
                'team_name' => '송파 스타즈',
                'sport' => '축구',
                'city' => '서울',
                'district' => '송파구',
                'owner_user_id' => $createdUsers[2]->id, // 박민수
                'wins' => 4,
                'draws' => 3,
                'losses' => 1,
                'points' => 15,
            ],
            [
                'team_name' => '강동 풋살 클럽',
                'sport' => '풋살',
                'city' => '서울',
                'district' => '강동구',
                'owner_user_id' => $createdUsers[4]->id, // 정현우
                'wins' => 6,
                'draws' => 1,
                'losses' => 2,
                'points' => 19,
            ],
            [
                'team_name' => '성남 레전드',
                'sport' => '축구',
                'city' => '경기',
                'district' => '성남시',
                'owner_user_id' => $createdUsers[6]->id, // 윤태호
                'wins' => 3,
                'draws' => 4,
                'losses' => 2,
                'points' => 13,
            ],
            [
                'team_name' => '수원 풋살 마스터즈',
                'sport' => '풋살',
                'city' => '경기',
                'district' => '수원시',
                'owner_user_id' => $createdUsers[8]->id, // 강동현
                'wins' => 7,
                'draws' => 0,
                'losses' => 1,
                'points' => 21,
            ],
            [
                'team_name' => '인천 바다 FC',
                'sport' => '축구',
                'city' => '인천',
                'district' => '연수구',
                'owner_user_id' => $createdUsers[10]->id, // 조성민
                'wins' => 2,
                'draws' => 5,
                'losses' => 2,
                'points' => 11,
            ],
        ];

        $createdTeams = [];
        foreach ($teams as $teamData) {
            $team = Team::firstOrCreate(
                [
                    'team_name' => $teamData['team_name'],
                    'sport' => $teamData['sport'],
                    'city' => $teamData['city'],
                    'district' => $teamData['district'],
                ],
                $teamData
            );
            $createdTeams[] = $team;
        }

        // 팀 멤버십 생성
        $memberships = [
            // 강남 유나이티드 FC 멤버들
            ['team_id' => $createdTeams[0]->id, 'user_id' => $createdUsers[1]->id, 'role' => 'member', 'status' => 'approved', 'joined_at' => now()->subDays(30)],
            ['team_id' => $createdTeams[0]->id, 'user_id' => $createdUsers[12]->id, 'role' => 'member', 'status' => 'approved', 'joined_at' => now()->subDays(25)],

            // 송파 스타즈 멤버들
            ['team_id' => $createdTeams[1]->id, 'user_id' => $createdUsers[3]->id, 'role' => 'member', 'status' => 'approved', 'joined_at' => now()->subDays(28)],
            ['team_id' => $createdTeams[1]->id, 'user_id' => $createdUsers[13]->id, 'role' => 'member', 'status' => 'approved', 'joined_at' => now()->subDays(20)],

            // 강동 풋살 클럽 멤버들
            ['team_id' => $createdTeams[2]->id, 'user_id' => $createdUsers[5]->id, 'role' => 'member', 'status' => 'approved', 'joined_at' => now()->subDays(35)],
            ['team_id' => $createdTeams[2]->id, 'user_id' => $createdUsers[14]->id, 'role' => 'member', 'status' => 'approved', 'joined_at' => now()->subDays(15)],

            // 성남 레전드 멤버들
            ['team_id' => $createdTeams[3]->id, 'user_id' => $createdUsers[7]->id, 'role' => 'member', 'status' => 'approved', 'joined_at' => now()->subDays(22)],

            // 수원 풋살 마스터즈 멤버들
            ['team_id' => $createdTeams[4]->id, 'user_id' => $createdUsers[9]->id, 'role' => 'member', 'status' => 'approved', 'joined_at' => now()->subDays(18)],

            // 인천 바다 FC 멤버들
            ['team_id' => $createdTeams[5]->id, 'user_id' => $createdUsers[11]->id, 'role' => 'member', 'status' => 'approved', 'joined_at' => now()->subDays(12)],
        ];

        foreach ($memberships as $membership) {
            TeamMember::firstOrCreate(
                [
                    'team_id' => $membership['team_id'],
                    'user_id' => $membership['user_id'],
                ],
                $membership
            );
        }

        // 더미 경기 생성
        $matches = [
            [
                'sport' => '축구',
                'city' => '서울',
                'district' => '강남구',
                'home_team_id' => $createdTeams[0]->id,
                'away_team_id' => $createdTeams[1]->id,
                'home_team_name' => '강남 유나이티드 FC',
                'away_team_name' => '송파 스타즈',
                'match_date' => now()->subDays(7),
                'match_time' => '14:00:00',
                'status' => '완료',
                'home_score' => 2,
                'away_score' => 1,
                'finalized_at' => now()->subDays(7)->addHours(2),
                'created_by' => $createdUsers[0]->id,
            ],
            [
                'sport' => '풋살',
                'city' => '서울',
                'district' => '강동구',
                'home_team_id' => $createdTeams[2]->id,
                'away_team_id' => $createdTeams[4]->id,
                'home_team_name' => '강동 풋살 클럽',
                'away_team_name' => '수원 풋살 마스터즈',
                'match_date' => now()->subDays(5),
                'match_time' => '16:00:00',
                'status' => '완료',
                'home_score' => 3,
                'away_score' => 4,
                'finalized_at' => now()->subDays(5)->addHours(1.5),
                'created_by' => $createdUsers[4]->id,
            ],
            [
                'sport' => '축구',
                'city' => '경기',
                'district' => '성남시',
                'home_team_id' => $createdTeams[3]->id,
                'away_team_id' => $createdTeams[5]->id,
                'home_team_name' => '성남 레전드',
                'away_team_name' => '인천 바다 FC',
                'match_date' => now()->subDays(3),
                'match_time' => '15:00:00',
                'status' => '완료',
                'home_score' => 1,
                'away_score' => 1,
                'finalized_at' => now()->subDays(3)->addHours(2),
                'created_by' => $createdUsers[6]->id,
            ],
            [
                'sport' => '축구',
                'city' => '서울',
                'district' => '강남구',
                'home_team_id' => $createdTeams[0]->id,
                'away_team_id' => null,
                'home_team_name' => '강남 유나이티드 FC',
                'away_team_name' => null,
                'match_date' => now()->addDays(3),
                'match_time' => '14:00:00',
                'status' => '예정',
                'home_score' => null,
                'away_score' => null,
                'finalized_at' => null,
                'created_by' => $createdUsers[0]->id,
            ],
            [
                'sport' => '풋살',
                'city' => '서울',
                'district' => '강동구',
                'home_team_id' => $createdTeams[2]->id,
                'away_team_id' => null,
                'home_team_name' => '강동 풋살 클럽',
                'away_team_name' => null,
                'match_date' => now()->addDays(5),
                'match_time' => '16:00:00',
                'status' => '예정',
                'home_score' => null,
                'away_score' => null,
                'finalized_at' => null,
                'created_by' => $createdUsers[4]->id,
            ],
        ];

        foreach ($matches as $match) {
            GameMatch::create($match);
        }

        echo "더미 데이터 생성 완료!\n";
        echo "사용자: " . count($createdUsers) . "명\n";
        echo "팀: " . count($createdTeams) . "개\n";
        echo "팀 멤버십: " . count($memberships) . "개\n";
        echo "경기: " . count($matches) . "개\n";
    }
}
