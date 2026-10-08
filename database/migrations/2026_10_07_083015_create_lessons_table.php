<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->string('title');
            $table->enum('lesson_type', ['video', 'document', 'quiz'])->default('video');
            $table->enum('video_type', ['youtube', 'vimeo', 'upload'])->nullable();
            $table->string('video_url')->nullable();
            $table->string('duration')->nullable();
            $table->string('attachment')->nullable();
            $table->longText('summary')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
