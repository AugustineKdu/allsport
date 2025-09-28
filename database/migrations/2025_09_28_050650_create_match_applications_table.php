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
        Schema::create('match_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('applied_by')->constrained('users')->onDelete('cascade')->comment('신청한 사용자');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->text('message')->nullable()->comment('신청 메시지');
            $table->string('contact_phone')->nullable()->comment('연락처');
            $table->string('contact_person')->nullable()->comment('연락 담당자');
            $table->json('availability')->nullable()->comment('가능한 시간대');
            $table->timestamp('applied_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['match_id', 'status']);
            $table->index(['team_id', 'status']);
            $table->unique(['match_id', 'team_id']); // 한 경기에 한 팀당 하나의 신청만
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_applications');
    }
};
