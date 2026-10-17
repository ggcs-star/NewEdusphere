
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('lesson_id')
                ->constrained('lessons')
                ->cascadeOnDelete();

            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('total_score')->default(0);

            $table->json('answers');

            $table->timestamp('submitted_at')->useCurrent();

            // Ek user ek lesson ka quiz sirf ek baar submit kar sakega.
            $table->unique(
                ['user_id', 'lesson_id'],
                'unique_user_quiz_attempt'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
