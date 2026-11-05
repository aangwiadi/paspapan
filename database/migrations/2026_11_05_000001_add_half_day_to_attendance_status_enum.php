<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // ALTER TYPE ... ADD VALUE cannot be used in the same transaction as the new value
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("ALTER TYPE attendance_status_enum ADD VALUE IF NOT EXISTS 'half_day'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Postgres cannot remove values from an enum type
    }
};
