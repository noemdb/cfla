<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferentialCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referential_currencies', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name')->nullable()->comment('Nombre');
            $table->string('symbol')->comment('Símbolo');
            $table->string('observations')->nullable()->comment('Observaciones');
            $table->boolean('status_cripto')->default(false)->comment('Cripto Moneda');
            $table->boolean('status_forgering')->default(false)->comment('Moneda extranjera');
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
        Schema::dropIfExists('referential_currencies');
    }
}
