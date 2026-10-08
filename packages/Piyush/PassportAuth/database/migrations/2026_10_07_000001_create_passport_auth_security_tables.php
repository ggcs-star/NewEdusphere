<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('passport_auth_login_histories')) Schema::create('passport_auth_login_histories', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id')->nullable(); $table->string('email')->nullable(); $table->boolean('success')->default(false); $table->string('failure_reason')->nullable(); $table->ipAddress('ip_address')->nullable(); $table->json('ip_chain')->nullable(); $table->text('user_agent')->nullable(); $table->string('device_id')->nullable(); $table->timestamp('created_at')->nullable();
            $table->index(['user_id','created_at']); $table->index(['email','created_at']);
        });
        if (!Schema::hasTable('passport_auth_devices')) Schema::create('passport_auth_devices', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('device_id'); $table->string('device_name')->nullable(); $table->string('platform',50)->nullable(); $table->string('app_version',50)->nullable(); $table->ipAddress('ip_address')->nullable(); $table->json('ip_chain')->nullable(); $table->text('user_agent')->nullable(); $table->timestamp('last_active_at')->nullable(); $table->timestamp('revoked_at')->nullable(); $table->timestamps(); $table->unique(['user_id','device_id']);
        });
        if (!Schema::hasTable('passport_auth_password_resets')) Schema::create('passport_auth_password_resets', function (Blueprint $table) {
            $table->id(); $table->string('email')->index(); $table->string('token',64)->index(); $table->timestamp('created_at')->nullable();
        });
        if (!Schema::hasTable('passport_auth_email_verifications')) Schema::create('passport_auth_email_verifications', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id')->unique(); $table->string('token',64)->index(); $table->timestamp('expires_at'); $table->timestamps();
        });
        if (!Schema::hasTable('passport_auth_otps')) Schema::create('passport_auth_otps', function (Blueprint $table) {
            $table->id(); $table->string('email')->index(); $table->string('otp_hash',64); $table->unsignedInteger('attempts')->default(0); $table->timestamp('expires_at'); $table->timestamp('used_at')->nullable(); $table->timestamp('created_at')->nullable();
        });
        if (!Schema::hasTable('passport_auth_security_audits')) Schema::create('passport_auth_security_audits', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id')->nullable(); $table->string('event',100); $table->ipAddress('ip_address')->nullable(); $table->json('ip_chain')->nullable(); $table->text('user_agent')->nullable(); $table->string('device_id')->nullable(); $table->json('metadata')->nullable(); $table->timestamp('created_at')->nullable();
            $table->index(['user_id','created_at']); $table->index(['event','created_at']);
        });
        if (!Schema::hasTable('passport_auth_social_accounts')) Schema::create('passport_auth_social_accounts', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('user_id'); $table->string('provider',50); $table->string('provider_id'); $table->string('email')->nullable(); $table->text('avatar')->nullable(); $table->timestamps(); $table->unique(['provider','provider_id']); $table->index('user_id');
        });
    }
    public function down(): void
    {
        foreach (['passport_auth_social_accounts','passport_auth_security_audits','passport_auth_otps','passport_auth_email_verifications','passport_auth_password_resets','passport_auth_devices','passport_auth_login_histories'] as $table) if (Schema::hasTable($table)) Schema::drop($table);
    }
};
