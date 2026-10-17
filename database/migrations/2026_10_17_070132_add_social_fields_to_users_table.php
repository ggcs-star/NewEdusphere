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
        Schema::table('users', function (Blueprint $table) {
            $table->string('short_title')->nullable()->after('biography');
            $table->string('facebook_link')->nullable()->after('short_title');
            $table->string('twitter_link')->nullable()->after('facebook_link');
            $table->string('linkedin_link')->nullable()->after('twitter_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['short_title', 'facebook_link', 'twitter_link', 'linkedin_link']);
        });
    }
};
