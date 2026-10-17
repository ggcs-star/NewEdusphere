<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Makes `role` the single source of truth for admin/instructor/user instead
 * of the overlapping role + is_instructor combo (which let a row be both
 * role=user and is_instructor=true at once). `is_instructor` is kept as a
 * derived, always-in-sync column (see User::booted()) so the many existing
 * call sites reading it don't need to change — it just can no longer drift
 * out of sync with role.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'instructor', 'user') NOT NULL DEFAULT 'user'");

        DB::table('users')
            ->where('role', 'user')
            ->where('is_instructor', true)
            ->update(['role' => 'instructor']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'instructor')
            ->update(['role' => 'user']);

        DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
    }
};
