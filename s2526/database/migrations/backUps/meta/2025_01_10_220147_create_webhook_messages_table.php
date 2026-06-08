<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_messages', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->string('from'); // Número de WhatsApp que envió el mensaje
            $table->string('to')->nullable(); // Número de WhatsApp de destino (si aplica)
            $table->string('wa_id')->nullable(); // ID de WhatsApp del contacto
            $table->string('profile_name')->nullable(); // Nombre del perfil del contacto
            $table->string('message_id')->unique(); // ID único del mensaje
            $table->text('body')->nullable(); // Contenido del mensaje
            $table->string('type')->default('text'); // Tipo de mensaje (texto, imagen, etc.)
            $table->timestamp('timestamp')->nullable(); // Timestamp del mensaje
            $table->string('messaging_product')->default('whatsapp'); // Producto de mensajería (por defecto WhatsApp)
            $table->string('phone_number_id')->nullable(); // ID del número de teléfono asociado
            $table->text('metadata')->nullable(); // Campo para almacenar datos adicionales en formato JSON
            $table->timestamps(); // Timestamps de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webhook_messages');
    }
}
