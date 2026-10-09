<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        $tableName = config('passport-auth.users_table', 'users');
        if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'email_verified_at')) {
            Schema::table($tableName, function (Blueprint $table) { $table->timestamp('email_verified_at')->nullable()->after('email'); });
        }
    }
    public function down(): void
    {
        $tableName = config('passport-auth.users_table', 'users');
        if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'email_verified_at')) Schema::table($tableName, function (Blueprint $table) { $table->dropColumn('email_verified_at'); });
    }
};
