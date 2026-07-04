<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollNivelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coll_nivels', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallinteger('coll_political_id')->unsigned();
            $table->string('name')->comment('Nombre');
            $table->smallinteger('order')->unsigned();
            $table->float('weighing',3,2)->default(1.00)->comment('Ponderación');
            $table->string('description')->nullable()->comment('Descripción');
            $table->enum('status',['true','false'])->default('true')->comment('Estado');
            $table->timestamps();
            $table->foreign('coll_political_id')->references('id')->on('coll_policals')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coll_nivels');
    }
}
