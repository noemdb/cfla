<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PLAN-ACTIVITIES-001 §2 — Información complementaria de una actividad.
 * 1:1 con activities (índice único). `text` = Markdown, `image_url` = URL
 * local (JPG) subida al storage public/activity-supplements/.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('activity_supplements')) {
            return;
        }

        Schema::create('activity_supplements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('activity_id');
            $table->mediumText('text')->nullable();
            $table->string('image_url', 255)->nullable();
            $table->timestamps();

            $table->unique('activity_id', 'uq_supplement_activity');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_supplements');
    }
};
