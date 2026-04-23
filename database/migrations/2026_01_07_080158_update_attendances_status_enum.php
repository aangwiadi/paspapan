<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the enum type first if it doesn't exist
        DB::statement("DO $$ BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'attendance_status_enum') THEN
                CREATE TYPE attendance_status_enum AS ENUM ('present', 'late', 'excused', 'sick', 'absent', 'rejected');
            END IF;
        END $$;");
        
        // Drop the default first, then convert column type, then set default back
        DB::statement("ALTER TABLE attendances ALTER COLUMN status DROP DEFAULT");
        DB::statement("ALTER TABLE attendances ALTER COLUMN status TYPE attendance_status_enum USING status::attendance_status_enum");
        DB::statement("ALTER TABLE attendances ALTER COLUMN status SET DEFAULT 'absent'::attendance_status_enum");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the default first, then convert back to varchar, then set default back
        DB::statement("ALTER TABLE attendances ALTER COLUMN status DROP DEFAULT");
        DB::statement("ALTER TABLE attendances ALTER COLUMN status TYPE varchar USING status::varchar");
        DB::statement("ALTER TABLE attendances ALTER COLUMN status SET DEFAULT 'absent'");
        
        // Drop the enum type
        DB::statement("DROP TYPE IF EXISTS attendance_status_enum");
    }
};
