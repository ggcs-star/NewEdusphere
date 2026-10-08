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
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('is_top_course')->default(false)->after('status');
            $table->enum('preview_video_type', ['youtube', 'vimeo', 'upload'])->default('youtube')->after('preview_video_url');
            $table->string('meta_keywords')->nullable()->after('rejection_reason');
            $table->text('meta_description')->nullable()->after('meta_keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['is_top_course', 'preview_video_type', 'meta_keywords', 'meta_description']);
        });
    }
};
