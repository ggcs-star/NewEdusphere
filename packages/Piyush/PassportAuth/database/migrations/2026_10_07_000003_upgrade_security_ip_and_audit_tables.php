<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('passport_auth_login_histories') && !Schema::hasColumn('passport_auth_login_histories', 'ip_chain')) {
            Schema::table('passport_auth_login_histories', function (Blueprint $table) {
                $table->json('ip_chain')->nullable()->after('ip_address');
            });
        }

        if (Schema::hasTable('passport_auth_devices') && !Schema::hasColumn('passport_auth_devices', 'ip_chain')) {
            Schema::table('passport_auth_devices', function (Blueprint $table) {
                $table->json('ip_chain')->nullable()->after('ip_address');
            });
        }

        if (!Schema::hasTable('passport_auth_security_audits')) {
            Schema::create('passport_auth_security_audits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('event', 100);
                $table->ipAddress('ip_address')->nullable();
                $table->json('ip_chain')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('device_id')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->index(['user_id', 'created_at']);
                $table->index(['event', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('passport_auth_security_audits')) {
            Schema::drop('passport_auth_security_audits');
        }

        if (Schema::hasTable('passport_auth_devices') && Schema::hasColumn('passport_auth_devices', 'ip_chain')) {
            Schema::table('passport_auth_devices', function (Blueprint $table) {
                $table->dropColumn('ip_chain');
            });
        }

        if (Schema::hasTable('passport_auth_login_histories') && Schema::hasColumn('passport_auth_login_histories', 'ip_chain')) {
            Schema::table('passport_auth_login_histories', function (Blueprint $table) {
                $table->dropColumn('ip_chain');
            });
        }
    }
};
