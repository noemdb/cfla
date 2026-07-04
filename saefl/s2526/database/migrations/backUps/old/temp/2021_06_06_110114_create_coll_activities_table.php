<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coll_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->smallinteger('coll_nivel_id')->nullable()->unsigned();
            $table->bigInteger('representant_id')->nullable()->unsigned()->comment('Representante');
            $table->string('description')->nullable()->comment('Descripción');
            $table->smallinteger('status_id')->unsigned()->comment('Estado');
            $table->enum('status_messege',['true','false'])->default('true')->comment('Estado de envío de email');
            $table->enum('status_call',['true','false'])->default('true')->comment('Estado de aprobación');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('coll_nivel_id')->references('id')->on('coll_nivels')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('representant_id')->references('id')->on('representants')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coll_activities');
    }
}
