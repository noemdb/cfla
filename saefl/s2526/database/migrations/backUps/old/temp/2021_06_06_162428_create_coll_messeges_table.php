<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollMessegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coll_messeges', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->smallinteger('coll_nivel_id')->unsigned();
            $table->text('subject')->comment('asunto');
            $table->text('title')->comment('Título');
            $table->text('subtitle')->comment('Subtítulo');
            $table->text('greeting')->comment('Saludo formal');
            $table->text('consider')->comment('Considerando');
            $table->text('sentence')->comment('Solicitud');
            $table->text('waiting')->comment('Esperando pronta respuesta');
            $table->text('footer')->comment('Despedida');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('coll_nivel_id')->references('id')->on('coll_nivels')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coll_messeges');
    }
}
