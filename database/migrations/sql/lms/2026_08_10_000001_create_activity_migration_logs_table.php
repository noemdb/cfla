<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_migration_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pestudio_id');
            $table->unsignedBigInteger('from_grado_id');
            $table->unsignedBigInteger('to_grado_id');
            $table->unsignedBigInteger('pensum_source_id')->nullable();
            $table->unsignedBigInteger('pensum_target_id')->nullable();
            $table->unsignedBigInteger('pev_source_id')->nullable();
            $table->unsignedBigInteger('pev_target_id')->nullable();
            $table->unsignedBigInteger('activity_source_id')->nullable();
            $table->unsignedBigInteger('activity_target_id')->nullable();
            $table->timestamp('copied_at');
            $table->timestamps();

            $table->index(['pestudio_id', 'from_grado_id', 'to_grado_id'], 'mig_scope_idx');
            $table->index('activity_source_id', 'mig_asrc_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_migration_logs');
    }
};
