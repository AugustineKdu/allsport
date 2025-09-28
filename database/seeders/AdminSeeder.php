<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 개발자용 관리자 계정
        User::updateOrCreate(
            ['email' => 'developer@allsports.com'],
            [
                'name' => '개발자',
                'nickname' => 'Developer',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'city' => '서울특별시',
                'district' => '강남구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'email_verified_at' => now(),
            ]
        );

        // 오너용 관리자 계정
        User::updateOrCreate(
            ['email' => 'owner@allsports.com'],
            [
                'name' => '오너',
                'nickname' => 'Owner',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'city' => '서울특별시',
                'district' => '강남구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'email_verified_at' => now(),
            ]
        );

        // 테스트용 일반 사용자 계정
        User::updateOrCreate(
            ['email' => 'test@allsports.com'],
            [
                'name' => '테스트유저',
                'nickname' => 'TestUser',
                'password' => Hash::make('password'),
                'role' => 'user',
                'city' => '서울특별시',
                'district' => '강남구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'email_verified_at' => now(),
            ]
        );

        // 테스트용 팀 오너 계정
        User::updateOrCreate(
            ['email' => 'teamowner@allsports.com'],
            [
                'name' => '팀오너',
                'nickname' => 'TeamOwner',
                'password' => Hash::make('password'),
                'role' => 'team_owner',
                'city' => '서울특별시',
                'district' => '강남구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('관리자 및 테스트 계정이 생성되었습니다:');
        $this->command->line('개발자 계정: developer@allsports.com / password');
        $this->command->line('오너 계정: owner@allsports.com / password');
        $this->command->line('테스트 계정: test@allsports.com / password');
        $this->command->line('팀오너 계정: teamowner@allsports.com / password');
    }
}
