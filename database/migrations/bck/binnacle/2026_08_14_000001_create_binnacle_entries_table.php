<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('binnacle_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('uuid', 36)->unique();

            $table->string('event_type', 50);
            $table->enum('event_category', ['authentication', 'user_action', 'system', 'security', 'error']);
            $table->enum('event_severity', ['debug', 'info', 'warning', 'critical', 'alert'])->default('info');

            $table->string('title', 255);
            $table->text('description')->nullable();

            $table->string('subject_type', 50)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_identifier', 100)->nullable();

            $table->string('object_type', 100)->nullable();
            $table->unsignedBigInteger('object_id')->nullable();
            $table->string('object_identifier', 255)->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_method', 10)->nullable();
            $table->text('request_url')->nullable();
            $table->string('request_id', 100)->nullable();
            $table->string('session_id', 100)->nullable();

            $table->char('country_code', 2)->nullable();
            $table->string('city', 100)->nullable();

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('changed_fields')->nullable();
            $table->json('metadata')->nullable();

            // Fase 4 (ADR-003): nullable hasta entonces, solo para severity critical/alert.
            $table->char('entry_hash', 64)->nullable();
            $table->char('previous_hash', 64)->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->index('event_type', 'idx_event_type');
            $table->index('event_category', 'idx_event_category');
            $table->index('event_severity', 'idx_event_severity');
            $table->index(['subject_type', 'subject_id', 'created_at'], 'idx_subject_time');
            $table->index(['object_type', 'object_id', 'created_at'], 'idx_object_time');
            $table->index('created_at', 'idx_created_at');
            $table->index('ip_address', 'idx_ip_address');
            $table->index('request_id', 'idx_request_id');
        });

        // Inmutabilidad en capa de BD (ADR-004). Schema Builder no soporta
        // triggers: se crean con SQL crudo. El job de archivado (Fase 3/4)
        // setea @binnacle_archive_process = 1 en su sesión para poder
        // UPDATE/DELETE; ningún código de aplicación normal lo hace.
        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_binnacle_no_update
BEFORE UPDATE ON binnacle_entries
FOR EACH ROW
BEGIN
    IF @binnacle_archive_process IS NULL OR @binnacle_archive_process != 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'binnacle_entries es de solo escritura (INSERT). UPDATE no permitido.';
    END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_binnacle_no_delete
BEFORE DELETE ON binnacle_entries
FOR EACH ROW
BEGIN
    IF @binnacle_archive_process IS NULL OR @binnacle_archive_process != 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'binnacle_entries es de solo escritura. DELETE solo vía proceso de archivado.';
    END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_binnacle_no_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_binnacle_no_update');
        Schema::dropIfExists('binnacle_entries');
    }
};
