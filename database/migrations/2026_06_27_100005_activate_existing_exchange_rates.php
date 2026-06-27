<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('currency_exchange_rates')
            ->whereIn('status', ['draft', 'approved'])
            ->update(['status' => 'active']);
    }

    public function down(): void
    {
        // Cannot reliably restore prior draft/approved distinction.
    }
};
