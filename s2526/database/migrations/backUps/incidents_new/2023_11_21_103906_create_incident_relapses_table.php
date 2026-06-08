<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentRelapsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incident_relapses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('duty_id')->unsigned();
            $table->integer('estudiant_id')->unsigned();
            $table->text('description')->comment('Descripción');
            $table->enum('status', ['primary','success','info','secondary','warning','danger','dark'])->default('primary');
            $table->boolean('status_notify')->default(false)->comment('Notificación');
            $table->boolean('status_active')->default(false)->comment('Estado');
            $table->timestamps();
            $table->foreign('duty_id')->references('id')->on('incident_duties')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incident_relapses');
    }
}
