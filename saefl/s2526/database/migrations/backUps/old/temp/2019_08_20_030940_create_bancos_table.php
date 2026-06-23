<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBancosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bancos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('institucion_id')->unsigned()->comment('Institución');
            $table->string('name')->comment('Nombres');
            $table->string('abbreviation',5)->comment('Abreviación - cinco (5) carácteres');
            $table->string('description')->comment('Descripción interna del banco');
            $table->string('logo')->deault('images/avatar/user_default.png')->comment('Logo del Banco');
            $table->string('number_id_bank')->comment('Número de identificación del banco');
            $table->string('number_acount_bank')->comment('Número de la cuenta');
            $table->float('commission_POS_bank',5,2)->comment('Porcentaje de comisión por uso de tarjeta de Débito');
            $table->float('commission_IGTF_bank',5,2)->comment('Porcentaje de IGTF');
            $table->enum('status_active_bank',['true','false'])->comment('Estado del Banco');
            $table->timestamps();
            $table->foreign('institucion_id')->references('id')->on('institucions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bancos');
    }
}
