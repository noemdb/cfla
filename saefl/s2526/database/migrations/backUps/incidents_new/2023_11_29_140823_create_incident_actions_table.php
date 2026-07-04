<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_actions', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->integer('incident_id')->unsigned();
            $table->integer('corrective_id')->unsigned();
            $table->string('description')->nullable();
            $table->boolean('status_selected')->default(true)->comment('Seleccionado');
            $table->timestamps();

            $table->foreign('incident_id')->references('id')->on('incidents')->onDelete('cascade');
            $table->foreign('corrective_id')->references('id')->on('incident_correctives')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_actions');
    }
}
