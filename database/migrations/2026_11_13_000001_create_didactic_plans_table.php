<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('didactic_plans', function (Blueprint $table): void {
            $table->bigIncrements('id');

            $table->unsignedInteger('pestudio_id');
            $table->unsignedInteger('grado_id');
            $table->unsignedInteger('seccion_id');
            $table->unsignedInteger('lapso_id')->nullable();

            $table->date('period_start');
            $table->date('period_end');

            $table->string('axis', 150)->default('Alimentación, cultura e identidad');
            $table->string('project_title', 180)->nullable();
            $table->text('objective')->nullable();
            $table->json('ods')->nullable();

            $table->string('status', 20)->default('draft');
            $table->unsignedSmallInteger('version')->default(1);
            $table->string('source_mode', 20)->default('filtered_activities');

            $table->json('filter_snapshot');
            $table->char('filter_hash', 64);
            $table->char('universe_hash', 64);
            $table->unsignedSmallInteger('filtered_count')->default(0);
            $table->unsignedSmallInteger('selected_count')->default(0);

            $table->text('notes')->nullable();
            $table->text('pdf_notes')->nullable();
            $table->char('checksum', 64)->nullable();
            $table->unsignedInteger('lock_version')->default(0);
            $table->string('approved_key', 64)->nullable();

            $table->unsignedInteger('created_by');
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('superseded_by_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['seccion_id', 'lapso_id', 'period_start', 'period_end', 'version'],
                'uq_didactic_plan_version'
            );
            $table->unique('approved_key', 'uq_didactic_plan_approved_key');
            $table->index(
                ['pestudio_id', 'grado_id', 'seccion_id', 'period_start'],
                'idx_didactic_plan_context'
            );
            $table->index(['status', 'period_start'], 'idx_didactic_plan_status');

            $table->foreign('pestudio_id', 'fk_dp_pestudio')
                ->references('id')->on('pestudios')->restrictOnDelete();
            $table->foreign('grado_id', 'fk_dp_grado')
                ->references('id')->on('grados')->restrictOnDelete();
            $table->foreign('seccion_id', 'fk_dp_seccion')
                ->references('id')->on('seccions')->restrictOnDelete();
            $table->foreign('lapso_id', 'fk_dp_lapso')
                ->references('id')->on('lapsos')->nullOnDelete();
            $table->foreign('created_by', 'fk_dp_created_by')
                ->references('id')->on('users')->restrictOnDelete();
            $table->foreign('updated_by', 'fk_dp_updated_by')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by', 'fk_dp_approved_by')
                ->references('id')->on('users')->nullOnDelete();
            $table->foreign('superseded_by_id', 'fk_dp_superseded_by')
                ->references('id')->on('didactic_plans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('didactic_plans');
    }
};
