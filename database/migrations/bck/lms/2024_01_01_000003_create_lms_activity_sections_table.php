<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_activity_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index(['activity_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_activity_sections');
    }
};
