<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('city', 120)->nullable()->after('phone');
            $table->string('country', 120)->nullable()->after('city');
        });

        Schema::table('tutor_profiles', function (Blueprint $table) {
            $table->boolean('offers_trial')->default(false)->after('hourly_rate');
            $table->decimal('trial_price', 8, 2)->default(0)->after('offers_trial');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_trial')->default(false)->after('duration_minutes');
        });

        Schema::create('tutor_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_profile_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('issuer')->nullable();
            $table->string('file_path');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending')->index();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('student_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['student_profile_id', 'subject_id']);
        });

        Schema::create('user_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('blocked_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['blocker_id', 'blocked_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_blocks');
        Schema::dropIfExists('student_subjects');
        Schema::dropIfExists('tutor_certificates');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('is_trial');
        });

        Schema::table('tutor_profiles', function (Blueprint $table) {
            $table->dropColumn(['offers_trial', 'trial_price']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['city', 'country']);
        });
    }
};
