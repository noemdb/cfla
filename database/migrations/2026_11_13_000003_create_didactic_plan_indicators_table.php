<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('didactic_plan_indicators', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('didactic_plan_id');
            $table->unsignedBigInteger('didactic_plan_session_id')->nullable();

            $table->string('name', 191);
            $table->decimal('weighting', 4, 0)->nullable();
            $table->boolean('is_quantitative')->default(false);
            $table->string('origin', 20)->default('manual');
            $table->unsignedBigInteger('source_achievement_id')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['didactic_plan_id', 'sort_order'], 'idx_dpi_plan');
            $table->index(['didactic_plan_session_id', 'sort_order'], 'idx_dpi_session');
            $table->index('source_achievement_id', 'idx_dpi_source');

            $table->foreign('didactic_plan_id', 'fk_dpi_plan')
                ->references('id')->on('didactic_plans')->cascadeOnDelete();
            $table->foreign('didactic_plan_session_id', 'fk_dpi_session')
                ->references('id')->on('didactic_plan_sessions')->cascadeOnDelete();
            $table->foreign('source_achievement_id', 'fk_dpi_source_achievement')
                ->references('id')->on('achievements')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('didactic_plan_indicators');
    }
};
