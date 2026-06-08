<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLapsosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lapsos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('Código');
            $table->string('code_sm')->comment('Abreviación');
            $table->string('name')->comment('Nombre');
            $table->date('finicial')->comment('Fecha inicial');
            $table->date('ffinal')->comment('Fecha final');
            $table->softDeletes();
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
        Schema::dropIfExists('lapsos');
    }
}
