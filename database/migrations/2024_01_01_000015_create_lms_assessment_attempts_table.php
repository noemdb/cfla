<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_assessment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('lms_activity_assessments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->decimal('score', 8, 2)->nullable();
            $table->enum('status', ['IN_PROGRESS', 'SUBMITTED', 'GRADED'])->default('IN_PROGRESS');
            $table->dateTime('started_at')->useCurrent();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->unsignedInteger('time_spent_secs')->nullable();

            $table->unique(['assessment_id', 'student_id', 'attempt_number']);
            $table->index(['student_id', 'status']);
            $table->index(['assessment_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_assessment_attempts');
    }
};
