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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('sport');
            $table->string('city');
            $table->string('district');
            $table->foreignId('home_team_id')->constrained('teams');
            $table->foreignId('away_team_id')->constrained('teams');
            $table->string('home_team_name');
            $table->string('away_team_name');
            $table->date('match_date');
            $table->time('match_time');
            $table->enum('status', ['예정', '진행중', '완료', '취소'])->default('예정');
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Indexes for performance
            $table->index(['sport', 'city', 'district']);
            $table->index(['match_date', 'status']);
            $table->index('home_team_id');
            $table->index('away_team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
