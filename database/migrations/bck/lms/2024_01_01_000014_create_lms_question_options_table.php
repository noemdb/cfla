<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('lms_assessment_questions')->cascadeOnDelete();
            $table->text('content');
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->text('feedback')->nullable();

            $table->index(['question_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_question_options');
    }
};
