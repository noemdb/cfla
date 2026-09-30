<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotencia de notificaciones a nivel de base de datos (ítem 8).
 *
 * El anti-spam por "no leídas" (`NotificationService::shouldNotify`) es un
 * SELECT seguido de un INSERT: dos jobs concurrentes (o el reintento de un job
 * que falló a mitad) pueden pasar ambos el SELECT y persistir el mismo aviso
 * dos veces. Aquí se añade una tabla de "reclamaciones": una fila por
 * (destinatario, tipo, asunto, ventana temporal) con índice ÚNICO, así que el
 * segundo que llegue choca contra la base de datos y se omite.
 *
 * La ventana se materializa como `bucket` = floor(epoch / (horas * 3600)): la
 * misma clave vuelve a estar libre en la ventana siguiente, de modo que un
 * recordatorio diario legitimate no se bloquea a sí mismo, y las filas viejas
 * se podan con `notifications:prune-dedupe`.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->addNotificationCategory();

        if (Schema::hasTable('notification_dedupe')) {
            return;
        }

        Schema::create('notification_dedupe', function (Blueprint $table) {
            $table->id();

            // sha1 de destinatario + tipo + asunto + bucket.
            $table->char('dedupe_key', 64)->unique();

            $table->string('notifiable_type')->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();
            // Clase de la notificación (App\Notifications\…) y tipo del payload
            // (activity_created…), para poder informar y podar sin el join.
            $table->string('notification_class')->nullable();
            $table->string('payload_type')->nullable();
            $table->unsignedBigInteger('bucket')->default(0);

            $table->timestamps();

            $table->index(['payload_type', 'created_at']);
            $table->index('notifiable_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_dedupe');
    }

    /**
     * `binnacle_entries.event_category` es un ENUM y no incluía la categoría de
     * notificaciones: al insertar 'notification' MySQL lo guardaba como '' en
     * silencio (valor inválido), y las métricas acababan fuera de todo filtro
     * por categoría. Se amplía el ENUM, que es aditivo y no altera las filas
     * existentes.
     */
    private function addNotificationCategory(): void
    {
        if (! Schema::hasTable('binnacle_entries') || ! Schema::hasColumn('binnacle_entries', 'event_category')) {
            return;
        }

        $current = DB::selectOne(
            "SELECT COLUMN_TYPE FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'binnacle_entries'
               AND COLUMN_NAME = 'event_category'"
        );

        if (! $current || str_contains($current->COLUMN_TYPE, "'notification'")) {
            return;
        }

        DB::statement(
            "ALTER TABLE `binnacle_entries`
             MODIFY `event_category` ENUM('authentication','user_action','system','security','error','notification')
             NOT NULL"
        );
    }
};
