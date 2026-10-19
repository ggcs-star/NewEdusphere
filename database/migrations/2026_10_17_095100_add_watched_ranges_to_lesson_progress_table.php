
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            if (!Schema::hasColumn('lesson_progress', 'watched_ranges')) {
                $table->json('watched_ranges')->nullable();
            }

            if (!Schema::hasColumn('lesson_progress', 'last_reported_position_seconds')) {
                $table->unsignedInteger('last_reported_position_seconds')
                    ->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            if (Schema::hasColumn('lesson_progress', 'last_reported_position_seconds')) {
                $table->dropColumn('last_reported_position_seconds');
            }

            if (Schema::hasColumn('lesson_progress', 'watched_ranges')) {
                $table->dropColumn('watched_ranges');
            }
        });
    }
};
