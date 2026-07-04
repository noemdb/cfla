<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_profiles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('section_diagnostic_report_id');

            $table->text('strengths');
            $table->text('needs');
            $table->text('attitudinal_factors');
            $table->text('cognitive_summary');
            $table->text('potential_barriers');

            $table->enum('dominant_processing_style', [
                'empirista-inductivo',
                'racionalista-deductivo',
                'introspectivo-vivencial'
            ]);

            $table->enum('dominant_learning_style', [
                'visual',
                'auditivo',
                'kinestesico'
            ]);

            $table->timestamps();

            $table->foreign('section_diagnostic_report_id')
                ->references('id')
                ->on('section_diagnostic_reports')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('section_profiles');
    }
}
