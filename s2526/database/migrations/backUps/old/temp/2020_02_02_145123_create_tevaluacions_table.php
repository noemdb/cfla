<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTevaluacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tevaluacions', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('code')->comment('Código');
            $table->string('name')->comment('Nombre');
            $table->string('description')->nullable()->comment('Descripción');
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
        Schema::dropIfExists('tevaluacions');
    }
}
