<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('student_status', ['present', 'absent', 'late', 'excused'])->nullable();
            $table->enum('tutor_status', ['present', 'absent', 'late'])->nullable();
            $table->dateTime('student_check_in')->nullable();
            $table->dateTime('student_check_out')->nullable();
            $table->dateTime('tutor_check_in')->nullable();
            $table->dateTime('tutor_check_out')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
