<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollPromisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coll_promises', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallinteger('coll_political_id')->unsigned();
            $table->bigInteger('representant_id')->unsigned()->comment('Representante');
            $table->date('date')->comment('Fecha del cumplimiento de la promesa');
            $table->float('ammount',16,2)->comment('Monto');
            $table->float('exchange_ammount',10,6)->comment('Monto Cambiario');
            $table->enum('status',['true','false'])->default('true')->comment('Estado');
            $table->string('description')->nullable()->comment('Descripción');
            $table->string('observation')->nullable()->comment('Observaciones');
            $table->timestamps();
            $table->foreign('coll_political_id')->references('id')->on('coll_policals')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('representant_id')->references('id')->on('representants')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coll_promises');
    }
}
