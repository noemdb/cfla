<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDescuentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Planes de descuento en pagos
        Schema::create('descuentos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descuento_name')->comment('Nombre del plan de descuento');
            $table->string('descuento_description')->comment('Descripción plan de descuento');
            $table->string('descuento_observations')->nullable()->comment('Observaciones de la Cuenta por Pagar');
            $table->enum('descuento_type',['Porcentaje','Monto'])->default('Porcentaje')->comment('Tipo de descuento');
            $table->float('descuento_ammount',6,2)->comment('Cantidad del descuento');       
            $table->enum('status_modifiable',['true','false'])->default('true')->comment('Permite manipular el costo al facturar');     
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
        Schema::dropIfExists('descuentos');
    }
}
