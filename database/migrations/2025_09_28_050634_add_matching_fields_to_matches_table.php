<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // 매칭 관련 필드 추가
            $table->string('matching_type')->default('local')->comment('매칭 유형: local(지역), national(전국)');
            $table->text('match_description')->nullable()->comment('경기 설명');
            $table->string('venue')->nullable()->comment('경기장 정보');
            $table->string('contact_phone')->nullable()->comment('연락처');
            $table->string('contact_person')->nullable()->comment('연락 담당자');
            $table->json('matching_preferences')->nullable()->comment('매칭 선호사항');
            $table->timestamp('matching_deadline')->nullable()->comment('매칭 마감일');
            $table->boolean('is_matching_open')->default(true)->comment('매칭 모집 여부');
            $table->integer('max_applicants')->nullable()->comment('최대 신청팀 수');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn([
                'matching_type',
                'match_description',
                'venue',
                'contact_phone',
                'contact_person',
                'matching_preferences',
                'matching_deadline',
                'is_matching_open',
                'max_applicants'
            ]);
        });
    }
};
