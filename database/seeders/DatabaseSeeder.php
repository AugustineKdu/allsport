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
            FixedTestAccountsSeeder::class, // 고정 테스트 계정들 생성
        ]);
    }
}
