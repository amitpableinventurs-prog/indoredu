<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subject_categories', function (Blueprint $table) {
            // e.g. 'CBSE', 'MP Board' for school classes; university name for B.E./M.Tech branches.
            $table->string('board')->nullable()->after('icon');
            $table->string('university')->nullable()->after('board');
            // 'school', 'undergraduate', 'postgraduate' — null for the original generic categories.
            $table->string('education_level')->nullable()->after('university');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->longText('syllabus')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('syllabus');
        });

        Schema::table('subject_categories', function (Blueprint $table) {
            $table->dropColumn(['board', 'university', 'education_level']);
        });
    }
};
