<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create {name} {email} {password}';
    protected $description = '관리자 계정 생성';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        // 이메일 중복 확인
        if (User::where('email', $email)->exists()) {
            $this->error('이미 존재하는 이메일입니다.');
            return 1;
        }

        try {
            $admin = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'nickname' => $name,
                'city' => '서울특별시',
                'district' => '강남구',
                'selected_sport' => '축구',
                'onboarding_done' => true,
                'email_verified_at' => now(),
            ]);

            $this->info("관리자 계정이 생성되었습니다:");
            $this->line("이름: {$admin->name}");
            $this->line("이메일: {$admin->email}");
            $this->line("역할: {$admin->role}");

            return 0;
        } catch (\Exception $e) {
            $this->error('관리자 계정 생성 실패: ' . $e->getMessage());
            return 1;
        }
    }
}
