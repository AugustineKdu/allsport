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
        Schema::create('match_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inviting_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('invited_team_id')->constrained('teams')->onDelete('cascade');
            $table->date('proposed_date');
            $table->time('proposed_time');
            $table->string('proposed_venue');
            $table->text('message')->nullable();
            $table->string('contact_phone')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->timestamps();

            // Ensure unique pending invitations between same teams
            $table->unique(['inviting_team_id', 'invited_team_id', 'status'], 'unique_pending_invitation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_invitations');
    }
};
