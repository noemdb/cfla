<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSendMailLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('send_mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('resend_id')->unique()->index(); // ID del proveedor de email
            $table->string('service_provider')->index(); // brevo, jetmail, sendpulse, resend
            $table->string('from');
            $table->string('to')->index();
            $table->string('subject');
            $table->longText('html');
            $table->longText('text')->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->string('status')->default('pending')->index(); // pending, sent, delivered, opened, clicked, bounced, complained, unsubscribed, rejected, failed
            $table->json('events')->nullable(); // Historial de eventos
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('complained_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            
            // Campos específicos para el contexto de cobranzas
            $table->unsignedBigInteger('collection_political_id')->nullable()->index();
            $table->string('representant_ci')->nullable()->index();
            $table->string('message_type')->default('collection_notice'); // Tipo de mensaje
            $table->timestamp('scheduled_at')->nullable(); // Cuándo fue programado
            $table->json('response_data')->nullable(); // Respuesta completa del proveedor
            
            $table->timestamps();
            
            // Índices compuestos para consultas frecuentes
            $table->index(['service_provider', 'status']);
            $table->index(['collection_political_id', 'representant_ci']);
            $table->index(['status', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('send_mail_logs');
    }
}
