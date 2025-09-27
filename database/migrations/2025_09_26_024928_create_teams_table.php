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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('team_name');
            $table->string('team_name_canon');
            $table->string('slug')->unique();
            $table->string('sport');
            $table->string('city');
            $table->string('district');
            $table->foreignId('owner_user_id')->constrained('users');
            $table->integer('wins')->default(0);
            $table->integer('draws')->default(0);
            $table->integer('losses')->default(0);
            $table->integer('points')->default(0);
            $table->string('join_code', 6)->nullable()->unique();
            $table->timestamps();

            // Unique constraint to prevent duplicate teams in same region/sport
            $table->unique(['sport', 'city', 'district', 'team_name_canon']);

            // Indexes for performance
            $table->index(['sport', 'city', 'district']);
            $table->index('points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
