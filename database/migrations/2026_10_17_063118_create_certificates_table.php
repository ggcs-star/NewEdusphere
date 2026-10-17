
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->restrictOnDelete();

            $table->foreignId('certificate_template_id')
                ->nullable()
                ->constrained('certificate_templates')
                ->nullOnDelete();

            $table->string('certificate_number', 64)->unique();

            // Snapshot of student/course details at issuance time.
            $table->json('data')->nullable();

            $table->timestamp('issued_at');

            $table->timestamps();

            // Only one certificate per student per course.
            $table->unique(
                ['user_id', 'course_id'],
                'certificates_user_course_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
