<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->BigIncrements('id');
            $table->bigInteger('representant_id')->unsigned()->comment('Representate');
            $table->longText('data');
            // $table->string('method',16)->comment('Método request');
            // $table->string('description',124)->comment('Descripción');
            // $table->date('date_transaction')->comment('Fecha de la transacción');
            // $table->float('ingreso_ammount',8,2)->comment('Monto del Ingreso');
            // $table->string('ingreso_observations')->nullable()->comment('Observaciones del Ingreso');
            // $table->string('person_bill_ci')->nullable()->comment('Cédula de la Persona a quien se le registrará el pago');
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
        Schema::dropIfExists('transactions');
    }
}
