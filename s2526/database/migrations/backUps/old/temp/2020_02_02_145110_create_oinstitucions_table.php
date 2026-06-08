<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOinstitucionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oinstitucions', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code')->comment('Código');
            $table->string('name')->comment('Nombre');
            $table->string('description')->nullable()->comment('Descripción');
            $table->string('locations')->comment('Localización - Municipio');
            $table->string('state')->comment('Estado - E.F.');
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
        Schema::dropIfExists('oinstitucions');
    }
}
