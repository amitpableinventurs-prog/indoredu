<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE courses MODIFY level ENUM('beginner', 'intermediate', 'advanced', 'crash_course', 'all_levels') NOT NULL DEFAULT 'all_levels'");

        Schema::table('courses', function (Blueprint $table) {
            $table->index(['status', 'level'], 'courses_status_level_index');
            $table->index(['status', 'created_at'], 'courses_status_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_status_level_index');
            $table->dropIndex('courses_status_created_at_index');
        });

        DB::statement("UPDATE courses SET level = 'all_levels' WHERE level = 'crash_course'");
        DB::statement("ALTER TABLE courses MODIFY level ENUM('beginner', 'intermediate', 'advanced', 'all_levels') NOT NULL DEFAULT 'all_levels'");
    }
};
