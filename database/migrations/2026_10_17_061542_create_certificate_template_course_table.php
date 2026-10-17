
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_template_course', function (Blueprint $table) {
            $table->id();

            $table->foreignId('certificate_template_id')
                ->constrained('certificate_templates')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->timestamps();

            // A course can have only one assigned template.
            $table->unique('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_template_course');
    }
};
