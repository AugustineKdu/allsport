<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders for initial data
        $this->call([
            RegionSeeder::class,
            SportSeeder::class,
        ]);

        // Create a test admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'nickname' => '관리자',
            'city' => '서울',
            'district' => '송파구',
            'selected_sport' => '축구',
            'onboarding_done' => true,
            'role' => 'admin',
        ]);

        // Create a test regular user who completed onboarding
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'nickname' => '테스터',
            'city' => '서울',
            'district' => '송파구',
            'selected_sport' => '풋살',
            'onboarding_done' => true,
            'role' => 'user',
        ]);

        // Create a new user who hasn't completed onboarding
        User::factory()->create([
            'name' => 'New User',
            'email' => 'new@example.com',
            'nickname' => null,
            'city' => null,
            'district' => null,
            'selected_sport' => null,
            'onboarding_done' => false,
            'role' => 'user',
        ]);

        // Create additional test users with onboarding completed
        for ($i = 1; $i <= 5; $i++) {
            User::factory()->create([
                'name' => "User $i",
                'email' => "user$i@example.com",
                'nickname' => "사용자$i",
                'city' => '서울',
                'district' => '송파구',
                'selected_sport' => $i % 2 == 0 ? '축구' : '풋살',
                'onboarding_done' => true,
                'role' => 'user',
            ]);
        }
    }
}
