<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanDatabaseCommand extends Command
{
    protected $signature = 'db:clean';
    protected $description = '데이터베이스를 정리하고 깔끔한 테스트 데이터만 남김';

    public function handle()
    {
        $this->info('🧹 데이터베이스 정리 시작...');

        if (!$this->confirm('기존 데이터를 모두 삭제하고 정리된 테스트 데이터만 남기시겠습니까?')) {
            $this->info('작업이 취소되었습니다.');
            return 0;
        }

        try {
            // 데이터베이스 초기화 및 정리된 시더 실행
            $this->call('migrate:fresh', ['--seed' => true]);

            $this->info('');
            $this->info('✅ 데이터베이스 정리 완료!');
            $this->info('');
            $this->info('🔐 사용 가능한 계정:');
            $this->info('   - developer@allsports.com / password (관리자)');
            $this->info('   - owner@allsports.com / password (관리자)');
            $this->info('   - teamowner@allsports.com / password (팀 오너)');
            $this->info('   - test@allsports.com / password (일반 사용자)');
            $this->info('');
            $this->info('📊 생성된 데이터:');
            $this->info('   - 테스트 팀 1개 (테스트 팀 FC)');
            $this->info('   - 테스트 경기 1개 (예정)');
            $this->info('   - 팀 멤버 2명 (팀 오너 + 일반 사용자)');

            return 0;
        } catch (\Exception $e) {
            $this->error('데이터베이스 정리 실패: ' . $e->getMessage());
            return 1;
        }
    }
}
