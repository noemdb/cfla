<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssitObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assit_observations', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('assit_attendance_id');
			$table->string('description')->comment('Descripción');
            $table->timestamps();
			$table->foreign('assit_attendance_id')->references('id')->on('assit_attendances')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assit_observations');
    }
}
