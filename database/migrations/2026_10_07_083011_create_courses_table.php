<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('outcomes')->nullable();
            $table->json('requirements')->nullable();
            $table->string('language')->default('English');
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'all_levels'])->default('all_levels');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->boolean('is_free')->default(false);
            $table->string('thumbnail')->nullable();
            $table->string('preview_video_url')->nullable();
            $table->enum('status', ['draft', 'pending', 'active', 'rejected'])->default('draft');
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
