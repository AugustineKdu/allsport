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
            // Remove unnecessary columns from old matching system
            $table->dropColumn([
                'matching_type',
                'match_description', 
                'contact_phone',
                'contact_person',
                'matching_preferences',
                'matching_deadline',
                'is_matching_open',
                'max_applicants'
            ]);
            
            // Add notes column if it doesn't exist
            if (!Schema::hasColumn('matches', 'notes')) {
                $table->text('notes')->nullable()->after('venue');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            // Re-add the columns if needed (for rollback)
            $table->string('matching_type')->default('local');
            $table->text('match_description')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_person')->nullable();
            $table->json('matching_preferences')->nullable();
            $table->timestamp('matching_deadline')->nullable();
            $table->boolean('is_matching_open')->default(true);
            $table->integer('max_applicants')->nullable();
            
            $table->dropColumn('notes');
        });
    }
};