<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_responses', function (Blueprint $table) {
            $table->id(); // ID de la notificación
            $table->string('message'); // Mensaje general
            $table->string('ident'); // CI del usuario
            $table->string('phone'); // Teléfono del usuario
            $table->string('template')->nullable(); // Plantilla
            $table->string('messaging_product')->nullable(); // Producto de mensajería (WhatsApp)
            $table->string('contact_input')->nullable(); // Número de teléfono ingresado
            $table->string('contact_wa_id')->nullable(); // Identificador en WhatsApp
            $table->string('message_id')->nullable(); // ID del mensaje en WhatsApp
            $table->string('message_status')->nullable(); // Estado del mensaje
            $table->string('json')->nullable(); // Estado del mensaje
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meta_responses');
    }
}
