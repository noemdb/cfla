<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('didactic_plan_sources', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('didactic_plan_id');
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('pevaluacion_id')->nullable();

            $table->char('activity_checksum', 64)->nullable();
            $table->json('fields_used')->nullable();
            $table->text('excerpt')->nullable();
            $table->boolean('included')->default(true);

            $table->timestamps();

            $table->unique(['didactic_plan_id', 'activity_id'], 'uq_dps_plan_activity');
            $table->index('activity_id', 'idx_dps_sources_activity');
            $table->index('pevaluacion_id', 'idx_dps_sources_pevaluacion');

            $table->foreign('didactic_plan_id', 'fk_dpsrc_plan')
                ->references('id')->on('didactic_plans')->cascadeOnDelete();
            $table->foreign('activity_id', 'fk_dpsrc_activity')
                ->references('id')->on('activities')->nullOnDelete();
            $table->foreign('pevaluacion_id', 'fk_dpsrc_pevaluacion')
                ->references('id')->on('pevaluacions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('didactic_plan_sources');
    }
};
