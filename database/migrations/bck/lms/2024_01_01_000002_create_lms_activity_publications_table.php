<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_activity_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->unique()->constrained('activities')->cascadeOnDelete();
            $table->unsignedInteger('published_by');
            $table->foreign('published_by')->references('id')->on('users')->restrictOnDelete();
            $table->enum('status', ['DRAFT', 'SCHEDULED', 'PUBLISHED', 'ARCHIVED'])->default('DRAFT');
            $table->dateTime('publish_at')->nullable();
            $table->dateTime('unpublish_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->boolean('allow_comments')->default(true);
            $table->boolean('allow_downloads')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_activity_publications');
    }
};
