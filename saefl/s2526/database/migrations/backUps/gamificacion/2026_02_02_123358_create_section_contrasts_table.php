<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionContrastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_contrasts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('section_diagnostic_report_id');

            $table->text('gaps');

            $table->json('critical_subjects')->nullable();

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
        Schema::dropIfExists('section_contrasts');
    }
}
