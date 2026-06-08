<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancellationFieldsToRegistroPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('registro_pagos', function (Blueprint $table) {
            // Determina si el pago puede ser anulado
            $table->boolean('cancellable')->after('status_prepayment')->default(false)->comment('Indica si el pago puede ser anulado');

            // ID del usuario que aprueba la anulación
            $table->unsignedBigInteger('approval_user_id')->after('status_prepayment')->nullable()->comment('ID del usuario que aprueba la anulación');

            // ID del usuario que anula el pago
            $table->unsignedBigInteger('cancellation_user_id')->after('status_prepayment')->nullable()->comment('ID del usuario que anula el pago');

            // Fecha de la anulación
            $table->timestamp('cancelled_at')->after('status_prepayment')->nullable()->comment('Fecha y hora en que se anuló el pago');

            // Fecha de la aprobación de la anulación
            $table->timestamp('approval_date')->after('status_prepayment')->nullable()->comment('Fecha y hora en que se aprobó la anulación');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('registro_pagos', function (Blueprint $table) {
            $table->dropColumn('cancellable');
            $table->dropColumn('approval_user_id');
            $table->dropColumn('cancellation_user_id');
            $table->dropColumn('cancelled_at');
            $table->dropColumn('approval_date');
        });
    }
}
