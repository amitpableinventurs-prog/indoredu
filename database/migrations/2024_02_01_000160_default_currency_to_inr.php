<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE tutor_profiles MODIFY currency CHAR(3) NOT NULL DEFAULT 'INR'");
        DB::statement("ALTER TABLE payments MODIFY currency CHAR(3) NOT NULL DEFAULT 'INR'");
        DB::statement("ALTER TABLE payouts MODIFY currency CHAR(3) NOT NULL DEFAULT 'INR'");

        // Backfill any existing rows still tagged USD from before the platform switched to INR.
        DB::table('tutor_profiles')->where('currency', 'USD')->update(['currency' => 'INR']);
        DB::table('payments')->where('currency', 'USD')->update(['currency' => 'INR']);
        DB::table('payouts')->where('currency', 'USD')->update(['currency' => 'INR']);
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tutor_profiles MODIFY currency CHAR(3) NOT NULL DEFAULT 'USD'");
        DB::statement("ALTER TABLE payments MODIFY currency CHAR(3) NOT NULL DEFAULT 'USD'");
        DB::statement("ALTER TABLE payouts MODIFY currency CHAR(3) NOT NULL DEFAULT 'USD'");
    }
};
