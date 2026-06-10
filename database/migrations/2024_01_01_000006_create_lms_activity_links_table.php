<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_activity_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignId('added_by')->constrained('users')->restrictOnDelete();
            $table->string('title');
            $table->string('url', 1000);
            $table->enum('link_type', ['REFERENCE', 'VIDEO', 'TOOL', 'DOCUMENT', 'OTHER'])->default('REFERENCE');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_activity_links');
    }
};
