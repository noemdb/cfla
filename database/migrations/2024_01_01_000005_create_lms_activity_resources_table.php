<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_activity_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('lms_media_library')->restrictOnDelete();
            $table->unsignedInteger('uploaded_by');
            $table->foreign('uploaded_by')->references('id')->on('users')->restrictOnDelete();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();

            $table->index(['activity_id', 'is_visible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_activity_resources');
    }
};
