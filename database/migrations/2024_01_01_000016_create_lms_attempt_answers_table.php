<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('lms_assessment_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('lms_assessment_questions')->restrictOnDelete();
            $table->json('selected_ids')->nullable();
            $table->text('text_answer')->nullable();
            $table->decimal('points_awarded', 6, 2)->nullable();
            $table->boolean('is_correct')->nullable();

            $table->unique(['attempt_id', 'question_id']);
            $table->index('attempt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_attempt_answers');
    }
};
