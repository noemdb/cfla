<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollDebtorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coll_debtors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('representant_id')->unsigned()->comment('Representante');
            $table->smallinteger('coll_nivel_id')->unsigned();
            $table->timestamps();
            $table->foreign('representant_id')->references('id')->on('representants')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('coll_debtors');
    }
}
