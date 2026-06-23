<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstitucionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // Datos basicos de la institucion
    public function up()
    {
        Schema::create('institucions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('Nombre de la institución');
            $table->string('code',24)->nullable()->comment('Código');
            $table->string('code_oficial',24)->nullable()->comment('Código oficial publico');
            $table->string('code_private',24)->nullable()->comment('Código oficial privado');
            $table->string('legalname')->comment('Nombre jurídico de la institución');
            $table->string('rif_institution',16)->unique()->comment('RIF jurídico de la institución');
            $table->string('email_institution')->unique()->comment('Contraseña de correo electrónico (Solo cuentas GMAIL)');
            $table->string('phone',16)->nullable()->comment('Número de teléfono 1');
            $table->string('phone2',16)->nullable()->comment('Número de teléfono 2');
            $table->string('phone3',16)->nullable()->comment('Número de teléfono 3');
            $table->string('address')->nullable()->comment('Dirección');
            $table->string('town_hall')->nullable()->comment('Municipio');
            $table->string('city')->nullable()->comment('Ciudad');
            $table->string('state')->nullable()->comment('Estado');
            $table->string('state_code',2)->nullable()->comment('Código del Estado');
            $table->string('country')->nullable()->comment('País');
            $table->string('password')->nullable()->comment('Contraseña de correo electrónico (Solo cuentas GMAIL)');
            $table->integer('format_bill')->nullable()->comment('Formato de factura');
            $table->string('number_fill_contingency',16)->nullable()->comment('Último número de factura de contingencia utilizado');
            $table->enum('status_enabled_number_a_bill',['true','false'])->nullable()->comment('Activar numeración automática de factura');
            $table->string('last_number_bill_config')->nullable()->comment('Último número de factura utilizado');
            $table->enum('status_print_bill_economical',['true','false'])->nullable()->comment('Imprimir facturas en modo económico');
            $table->enum('status_dont_allow_registration_if_insolvency',['true','false'])->comment('No permitir registro de inscripciones en caso de insolvencia');
            $table->enum('status_no_show_info_academic',['true','false'])->nullable()->comment('No mostrar información académica en caso de insolvencia');
            $table->enum('status_proof_of_payment',['true','false'])->nullable()->comment('Permitir registro de pagos con recibos electrónicos');
            $table->enum('status_credit_bills',['true','false'])->nullable()->comment('Permitir registro de facturas a crédito');
            $table->enum('status_printer_fiscal',['true','false'])->nullable()->comment('Utiliza impresora fiscal');
            $table->enum('status_print_number_bill',['true','false'])->nullable()->comment('Emitir numeración en factura');
            $table->enum('status_skip_discount',['true','false'])->nullable()->comment('Omitir descuentos al generar cuentas por cobrar en lote');
            $table->enum('status_enabled_inscription_academic',['true','false'])->nullable()->comment('Habilitar opción de inscripción académica simultánea');
            $table->string('concept_islr',4)->nullable()->comment('Concepto para retención de impuesto sobre la renta en pagos');
            $table->enum('status_apply_tax',['true','false'])->nullable()->comment('Activar cobro de impuesto por venta en facturación');
            $table->float('percent_tax',6,2)->nullable()->comment('Porcentaje de impuesto por venta');
            $table->string('provider_payonline')->nullable()->comment('Banco receptor de Pagos en Línea y a través del POS Virtual');
            $table->string('bank_payonline')->nullable()->comment('Porcentaje de impuesto por venta');
            $table->float('percent_comission_payonline',6,2)->nullable()->comment('Porcentaje de comisión por servicio para Pagos en Línea');
            $table->float('percent_POSVirtual',6,2)->nullable()->comment('Porcentaje por servicio para Pagos por POS Virtual');
            $table->enum('status_exchange_rate',['true','false'])->nullable()->comment('Activar cobro por tasa cambio');
            $table->float('ammount_exchange_rate',6,2)->nullable()->comment('Tasa de cambio');
            $table->text('observation_default_bill')->nullable()->comment('Observaciones por defecto en la factura');
            $table->text('observation_default_billing_notice')->nullable()->comment('Observaciones en avisos de cobro enviados a correos');
            $table->text('txt_contract_study')->nullable()->comment('Texto del contrato de servicio');
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
        Schema::dropIfExists('institucions');
    }
}
