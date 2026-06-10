<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_content_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('lms_activity_contents')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->restrictOnDelete();
            $table->boolean('viewed')->default(false);
            $table->dateTime('viewed_at')->nullable();
            $table->unsignedInteger('time_spent_secs')->default(0);
            $table->unique(['content_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_content_progress');
    }
};
