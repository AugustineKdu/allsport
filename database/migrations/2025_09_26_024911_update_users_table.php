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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable()->after('name');
            $table->string('city')->nullable()->after('nickname');
            $table->string('district')->nullable()->after('city');
            $table->string('selected_sport')->nullable()->after('district');
            $table->boolean('onboarding_done')->default(false)->after('selected_sport');
            $table->enum('role', ['user', 'team_owner', 'admin'])->default('user')->after('onboarding_done');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nickname', 'city', 'district', 'selected_sport', 'onboarding_done', 'role']);
        });
    }
};
