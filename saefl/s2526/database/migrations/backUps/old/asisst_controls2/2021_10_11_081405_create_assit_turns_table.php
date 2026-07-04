<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssitTurnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assit_turns', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('assit_day_id');
			$table->string('name')->comment('Nombre');
			$table->smallInteger('number')->unsigned()->comment('Hora');
            $table->timestamps();
			$table->foreign('assit_day_id')->references('id')->on('assit_days')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assit_turns');
    }
}
