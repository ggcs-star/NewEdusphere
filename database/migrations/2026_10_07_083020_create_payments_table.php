<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->enum('payment_method', ['paypal', 'stripe', 'free'])->default('free');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('admin_revenue', 10, 2)->default(0);
            $table->decimal('instructor_revenue', 10, 2)->default(0);
            $table->boolean('instructor_paid_out')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
