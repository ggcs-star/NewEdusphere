
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificate_templates')) {
            throw new RuntimeException(
                'certificate_templates table is missing. Check earlier migrations first.'
            );
        }

        // Keep compatibility with the earlier title-based migration.
        if (! Schema::hasColumn('certificate_templates', 'name')) {
            Schema::table('certificate_templates', function (Blueprint $table) {
                $table->string('name', 191)->nullable();
            });

            if (Schema::hasColumn('certificate_templates', 'title')) {
                DB::table('certificate_templates')
                    ->whereNull('name')
                    ->update(['name' => DB::raw('title')]);
            }
        }

        if (! Schema::hasColumn('certificate_templates', 'sort_order')) {
            Schema::table('certificate_templates', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0);
            });
        }

        // One course -> one template; one template -> many courses.
        if (! Schema::hasColumn('courses', 'certificate_template_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignId('certificate_template_id')
                    ->nullable()
                    ->constrained('certificate_templates')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Preserve data. Do not automatically remove columns that may now
        // be used by the application or by issued certificates.
    }
};
