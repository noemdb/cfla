<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiagReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('diag_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('estudiant_id')->constrained('estudiants'); // Assuming Estudiant uses BigInt, if not need to check. Usually standard Laravel 8+ is BigInt.
            $table->foreignId('diag_main_id')->constrained('diag_mains');
            $table->foreignId('referent_id')->constrained('diag_referents');

            $table->unsignedInteger('lapso_id');
            $table->foreign('lapso_id')->references('id')->on('lapsos');

            $table->string('status')->default('draft')->comment('draft, validated, signed');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diag_reports');
    }
}
