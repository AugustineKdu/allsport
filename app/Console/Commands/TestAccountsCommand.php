<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class TestAccountsCommand extends Command
{
    protected $signature = 'test:accounts';
    protected $description = '테스트 계정 정보 출력';

    public function handle()
    {
        $this->info('🔐 AllSports 테스트 계정 정보');
        $this->line('');

        // 관리자 계정들
        $this->info('👑 관리자 계정');
        $this->table(
            ['이메일', '비밀번호', '역할', '상태'],
            [
                ['developer@allsports.com', 'password', '개발자 관리자', '✅ 활성'],
                ['owner@allsports.com', 'password', '오너 관리자', '✅ 활성'],
            ]
        );

        // 테스트 계정들
        $this->info('🧪 테스트 계정');
        $this->table(
            ['이메일', '비밀번호', '역할', '상태'],
            [
                ['test@allsports.com', 'password', '일반 사용자', '✅ 활성'],
                ['teamowner@allsports.com', 'password', '팀 오너', '✅ 활성'],
            ]
        );

        // 현재 데이터 상태
        $this->info('📊 현재 데이터 상태');
        $this->table(
            ['항목', '개수', '상태'],
            [
                ['사용자', \App\Models\User::count() . '명', '✅ 활성'],
                ['팀', \App\Models\Team::count() . '개', '✅ 활성'],
                ['팀 멤버십', \App\Models\TeamMember::count() . '개', '✅ 활성'],
                ['경기', \App\Models\GameMatch::count() . '개', '✅ 활성'],
            ]
        );

        $this->line('');
        $this->info('📋 사용 가능한 기능');
        $this->line('• 관리자: 전체 시스템 관리, 사용자 관리, 지역/스포츠 설정');
        $this->line('• 팀 오너: 팀 생성, 경기 생성, 팀 관리');
        $this->line('• 일반 사용자: 팀 가입, 경기 참여');

        $this->line('');
        $this->info('🌐 접속 방법');
        $this->line('1. 웹 브라우저에서 애플리케이션 접속');
        $this->line('2. 로그인 페이지에서 위 계정 정보로 로그인');
        $this->line('3. 관리자 계정은 우측 상단 드롭다운에서 "관리자 대시보드" 클릭');

        $this->line('');
        $this->warn('⚠️  주의사항');
        $this->line('• 모든 계정의 비밀번호는 "password"입니다');
        $this->line('• 개발/테스트 환경 전용입니다');
        $this->line('• 프로덕션 환경에서는 다른 비밀번호를 사용하세요');

        return 0;
    }
}
