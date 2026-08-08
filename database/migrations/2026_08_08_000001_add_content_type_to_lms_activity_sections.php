<?php

use App\Models\app\Academy\Lms\LmsActivitySection;
use App\Services\Lms\LmsContentClassifier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Spec "Campo content_type en lms_activity_sections" — F1.
 *
 * Añade `content_type` (caché denormalizada derivada de los contenidos
 * visibles) + índice, y backfillea las filas existentes con el clasificador
 * centralizado (LmsContentClassifier::classifySection).
 *
 * `down()` elimina índice + columna (los datos se pueden recalcular con
 * `php8.2 artisan lms:sync-section-types` tras re-migrar).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_activity_sections', function (Blueprint $table) {
            $table->string('content_type', 30)
                ->nullable()
                ->after('description')
                ->comment('Tipo de contenido derivado de los contenidos visibles (caché, ver LmsContentClassifier)');
            $table->index('content_type');
        });

        // Backfill: clasificar las secciones existentes en una sola pasada.
        $classifier = app(LmsContentClassifier::class);

        LmsActivitySection::with('visibleContents.media')->chunkById(200, function ($sections) use ($classifier) {
            foreach ($sections as $section) {
                $section->content_type = $classifier->classifySection($section->visibleContents);
                $section->saveQuietly();
            }
        });
    }

    public function down(): void
    {
        Schema::table('lms_activity_sections', function (Blueprint $table) {
            $table->dropIndex(['content_type']);
            $table->dropColumn('content_type');
        });
    }
};
