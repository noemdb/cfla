<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdministrativasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrativas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('estudiant_id')->unique()->unsigned()->comment('Estudiante');
            $table->integer('user_id')->unsigned();
            $table->integer('planpago_id')->unsigned();
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->timestamps();
            $table->foreign('estudiant_id')->references('id')->on('estudiants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('planpago_id')->references('id')->on('planpagos')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('administrativas');
    }
}
