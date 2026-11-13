<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('didactic_plan_sessions', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('didactic_plan_id');

            $table->date('date');
            $table->unsignedTinyInteger('day_index');
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->string('title', 180);
            $table->string('thematic', 180)->nullable();
            $table->text('objective')->nullable();

            $table->text('start')->nullable();
            $table->text('development')->nullable();
            $table->text('close')->nullable();
            $table->text('resources')->nullable();

            $table->string('evaluation_instrument', 60)->nullable();
            $table->text('evaluation_description')->nullable();
            $table->text('observations')->nullable();
            $table->json('ods')->nullable();

            $table->boolean('is_non_teaching')->default(false);

            $table->unsignedBigInteger('source_activity_id')->nullable();
            $table->string('source_field', 40)->nullable();
            $table->text('source_excerpt')->nullable();
            $table->char('source_checksum', 64)->nullable();
            $table->decimal('confidence', 4, 2)->nullable();

            $table->timestamps();

            $table->index(['didactic_plan_id', 'date', 'sort_order'], 'idx_dps_plan_date');
            $table->index('source_activity_id', 'idx_dps_source');

            $table->foreign('didactic_plan_id', 'fk_dps_plan')
                ->references('id')->on('didactic_plans')->cascadeOnDelete();
            $table->foreign('source_activity_id', 'fk_dps_source_activity')
                ->references('id')->on('activities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('didactic_plan_sessions');
    }
};
