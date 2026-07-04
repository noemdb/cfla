<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name')->comment('Nombre');
            $table->string('type')->nullable()->comment('Tipo');
            $table->string('view')->nullable()->comment('Vista');
            $table->string('module')->nullable()->comment('Módulo');
            $table->float('weighing',3,2)->default(1.00)->comment('Ponderación');
            $table->enum('status',['true','false'])->default('true')->comment('Estado');
            $table->string('description')->nullable()->comment('Descripción');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statuses');
    }
}
