<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('company_assets', function (Blueprint $table) {
            $table->date('purchase_date')->nullable()->after('type');
            $table->decimal('purchase_cost', 15, 2)->nullable()->after('purchase_date');
            $table->date('expiration_date')->nullable()->after('purchase_cost');
        });

        // Safely alter ENUM using PostgreSQL syntax
        DB::statement("DO $$ BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'company_assets_status_pro') THEN
                CREATE TYPE company_assets_status_pro AS ENUM ('available', 'assigned', 'maintenance', 'lost', 'retired', 'sold', 'auctioned', 'disposed');
            END IF;
        END $$;");
        
        // Drop the default first, then convert column type, then set default back
        DB::statement("ALTER TABLE company_assets ALTER COLUMN status DROP DEFAULT");
        DB::statement("ALTER TABLE company_assets ALTER COLUMN status TYPE company_assets_status_pro USING status::company_assets_status_pro");
        DB::statement("ALTER TABLE company_assets ALTER COLUMN status SET DEFAULT 'available'::company_assets_status_pro");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_assets', function (Blueprint $table) {
            $table->dropColumn(['purchase_date', 'purchase_cost', 'expiration_date']);
        });

        DB::statement("DO $$ BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'company_assets_status_old') THEN
                CREATE TYPE company_assets_status_old AS ENUM ('available', 'assigned', 'maintenance', 'lost', 'retired');
            END IF;
        END $$;");
        
        // Drop the default first, then convert back to old enum, then set default back
        DB::statement("ALTER TABLE company_assets ALTER COLUMN status DROP DEFAULT");
        DB::statement("ALTER TABLE company_assets ALTER COLUMN status TYPE company_assets_status_old USING status::company_assets_status_old");
        DB::statement("ALTER TABLE company_assets ALTER COLUMN status SET DEFAULT 'available'::company_assets_status_old");
        
        // Drop the new enum type
        DB::statement("DROP TYPE IF EXISTS company_assets_status_pro");
    }
};
