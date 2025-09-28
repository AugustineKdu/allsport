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
        Schema::dropIfExists('match_applications');
        Schema::dropIfExists('match_requests');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create tables if needed for rollback
        Schema::create('match_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('applied_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->text('message')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_person')->nullable();
            $table->json('availability')->nullable();
            $table->timestamp('applied_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('match_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requesting_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('requested_team_id')->constrained('teams')->onDelete('cascade');
            $table->date('match_date');
            $table->time('match_time');
            $table->string('venue');
            $table->text('message')->nullable();
            $table->string('contact_phone')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }
};