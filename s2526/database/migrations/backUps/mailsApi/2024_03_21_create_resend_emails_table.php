<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resend_emails', function (Blueprint $table) {
            $table->id();
            $table->string('resend_id')->unique(); // ID de Resend
            $table->string('from');
            $table->string('to');
            $table->string('subject');
            $table->text('html')->nullable();
            $table->text('text')->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->json('events')->nullable(); // Para almacenar eventos del webhook
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resend_emails');
    }
};
