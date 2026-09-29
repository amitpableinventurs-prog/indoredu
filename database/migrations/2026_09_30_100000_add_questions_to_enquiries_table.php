<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->json('questions')->nullable()->after('title');
            $table->json('answers')->nullable()->after('tutor_reply');
            $table->text('message')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn(['questions', 'answers']);
        });
    }
};
