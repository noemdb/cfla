<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaders', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Identificador');
            $table->unsignedInteger('user_id')->comment('Usuario');
            $table->unsignedInteger('curricular_area')->comment('Área curricular a la que se asocia');
            $table->text('description')->nullable()->comment('Descripción breve del Jefe/Líder y sus funciones (opcional)');
            $table->boolean('is_active')->default(true)->comment('Indica si el Jefe/Líder está activo');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaders');
    }
}
