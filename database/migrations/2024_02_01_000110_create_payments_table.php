<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_enrollment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('gateway', ['stripe', 'paypal', 'razorpay'])->default('stripe');
            $table->string('gateway_transaction_id')->nullable()->index();
            $table->string('gateway_reference')->nullable();
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('USD');
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'partially_refunded'])->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_reason')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_profile_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('USD');
            $table->enum('method', ['bank_transfer', 'paypal'])->default('paypal');
            $table->enum('status', ['pending', 'processing', 'paid', 'failed'])->default('pending')->index();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('payments');
    }
};
