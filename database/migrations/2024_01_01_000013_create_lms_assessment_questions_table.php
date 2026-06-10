<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('lms_activity_assessments')->cascadeOnDelete();
            $table->enum('type', ['MULTIPLE_CHOICE', 'MULTIPLE_SELECT', 'TRUE_FALSE', 'SHORT_ANSWER', 'LONG_ANSWER'])->default('MULTIPLE_CHOICE');
            $table->text('content');
            $table->foreignId('media_id')->nullable()->constrained('lms_media_library')->nullOnDelete();
            $table->decimal('points', 6, 2)->default(1);
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->text('explanation')->nullable();
            $table->timestamps();

            $table->index(['assessment_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_assessment_questions');
    }
};
