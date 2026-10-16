<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->unsignedInteger('total_score')->default(0)->after('lesson_type');
        });

        // Backfill from whatever questions already exist, so existing
        // quizzes don't show 0 until their next edit.
        DB::table('lessons')
            ->where('lesson_type', 'quiz')
            ->get(['id'])
            ->each(function ($lesson) {
                $total = DB::table('questions')->where('lesson_id', $lesson->id)->sum('points');
                DB::table('lessons')->where('id', $lesson->id)->update(['total_score' => $total]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('total_score');
        });
    }
};
