<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_descriptions', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('reason_id')->unsigned()->comment('Motivo');
            $table->string('ambit')->comment('Ámbito');
            $table->string('name')->comment('Nombre');
            $table->timestamps();
            $table->foreign('reason_id')->references('id')->on('incident_reasons')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_descriptions');
    }
}
