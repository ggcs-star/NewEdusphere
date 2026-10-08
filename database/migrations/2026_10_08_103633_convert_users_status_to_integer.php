<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The admin panel's users.status was a string ('active'/'suspended'), but
 * the Passport auth package (piyush/passport-auth) reads it as an integer
 * flag — (int) $user->status !== 1 — which treats every string value as 0
 * and locks every account out at login. This converts the column to the
 * integer convention the auth package expects, preserving existing data.
 * Raw SQL (not ->change()) so this doesn't need doctrine/dbal installed.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users ADD COLUMN status_int TINYINT(1) NOT NULL DEFAULT 1 AFTER status");
        DB::statement("UPDATE users SET status_int = CASE WHEN status = 'active' THEN 1 ELSE 0 END");
        DB::statement("ALTER TABLE users DROP COLUMN status");
        DB::statement("ALTER TABLE users CHANGE status_int status TINYINT(1) NOT NULL DEFAULT 1");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users ADD COLUMN status_str VARCHAR(255) NOT NULL DEFAULT 'active' AFTER status");
        DB::statement("UPDATE users SET status_str = CASE WHEN status = 1 THEN 'active' ELSE 'suspended' END");
        DB::statement("ALTER TABLE users DROP COLUMN status");
        DB::statement("ALTER TABLE users CHANGE status_str status VARCHAR(255) NOT NULL DEFAULT 'active'");
    }
};
