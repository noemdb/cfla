<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_html_embeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('lms_activity_sections')->cascadeOnDelete();
            $table->unsignedInteger('added_by');
            $table->foreign('added_by')->references('id')->on('users')->restrictOnDelete();
            $table->string('title', 255)->nullable();
            $table->longText('html_content');
            $table->string('render_condition', 50)->default('ALWAYS');
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index(['activity_id', 'is_visible']);
            $table->index('section_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_html_embeds');
    }
};
