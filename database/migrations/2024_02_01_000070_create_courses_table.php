<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->default('all_levels');
            $table->decimal('price', 8, 2)->default(0);
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->unsignedInteger('total_sessions')->default(1);
            $table->boolean('is_group')->default(false);
            $table->unsignedInteger('max_students')->default(1);
            $table->string('cover_image')->nullable();
            $table->enum('status', ['draft', 'pending', 'published', 'rejected', 'archived'])->default('draft')->index();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['course_id', 'student_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('courses');
    }
};
