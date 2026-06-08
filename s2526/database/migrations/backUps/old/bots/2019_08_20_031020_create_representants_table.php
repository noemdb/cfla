<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepresentantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('representants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ci_representant')->unique()->comment('Cédula de identidad, Id temporal o pasaporte');
            $table->string('name')->nullable()->comment('Nombres');
            $table->string('phone')->nullable()->comment('Número de teléfono fijo');
            $table->string('cellphone')->nullable()->comment('Número de teléfono celular');
            $table->string('email')->nullable()->comment('Correo electrónico');
            $table->enum('status_active',['true','false'])->default('true')->comment('Estado');
            $table->softDeletes();
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
        Schema::dropIfExists('representants');
    }
}
