<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_activity_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('lms_activity_sections')->cascadeOnDelete();
            $table->enum('type', ['TEXT', 'VIDEO', 'AUDIO', 'IMAGE', 'PRESENTATION', 'HTML', 'EMBED', 'FILE_PREVIEW'])->default('TEXT');
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->foreignId('media_id')->nullable()->constrained('lms_media_library')->nullOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index(['section_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_activity_contents');
    }
};
