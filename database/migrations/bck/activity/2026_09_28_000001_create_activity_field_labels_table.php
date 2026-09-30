<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Etiquetas de Activity/Achievement por Peducativo (Opción 1).
 *
 * ADITIVA: solo crea `activity_field_labels` y la puebla con los labels
 * vigentes en producción al 2026-09-28 (base común + overrides por programa
 * en PEDUCATIVO_OVERRIDES, clave por nombre —estable entre instalaciones—).
 * No altera ninguna tabla de negocio.
 *
 * NOTA: esta migración ya corrió en producción; editarla solo afecta a
 * instalaciones frescas. En producción los valores se editan en el CRUD
 * (/app/planning/activity-labels).
 *
 * Las sub-etiquetas INICIO/DESARROLLO/CIERRE no se tocan (son marcadores
 * de parseo del campo `teaching`).
 */
return new class extends Migration
{
    /** Labels actuales de Activity (semilla inicial, editables luego). */
    public const ACTIVITY_LABELS = [
        'pevaluacion_id' => 'Plan de Evaluación',
        'finicial' => 'Fecha Inicial',
        'ffinal' => 'Fecha Final',
        'topic' => 'Tema generador y Énfasis',
        'thematic' => 'Tejido temático / Tema Indispensable',
        'references' => 'Referentes teórico prácticos y éticos',
        'teaching' => 'Enseñanza/Actividad Globalizada',
        'learning' => 'Aprendizaje',
        'description' => 'Actividad Evaluativa',
        'observations' => 'ODS / Sistematización',
        'comments' => 'Comentarios del Jefe de Área',
        'status' => 'Aprobación (1=Aprobado, 0=En revisión)',
        // Compuesto para la columna "Contenido" de los PDFs (no es columna).
        'topic_thematic_referentes' => 'Referentes teórico-prácticos',
        // Columna "ODS / Sistematización" de los PDFs (formato y resumen).
        'ODS_sistematizacion' => 'ODS / Sistematización',
        // Tab "Enseñanza y Evaluación" del form del profesor.
        'teaching_assessment' => 'Enseñanza y Evaluación',
    ];

    /** Labels actuales de Achievement (semilla inicial, editables luego). */
    public const ACHIEVEMENT_LABELS = [
        'activity_id' => 'Actividad',
        'name' => 'Nombre del indicador',
        'weighting' => 'Ponderación',
        'status_quantitative_weighting' => 'El indicador es ponderado (cuantitativo)',
    ];

    /**
     * Overrides por programa (nombre exacto de `peducativos.name`).
     * Estructura: programa => modelo => campo => [label, placeholder].
     * Valores vigentes en producción al 2026-09-28.
     */
    public const PEDUCATIVO_OVERRIDES = [
        'EDUCACION INICIAL' => [
            'activity' => [
                'topic' => ['label' => 'Componente', 'placeholder' => 'Componente'],
                'thematic' => ['label' => 'Aprendizaje a ser alcanzado', 'placeholder' => 'Aprendizaje a ser alcanzado'],
                'references' => ['label' => 'Nombre de los niños a ser evaluados', 'placeholder' => 'Nombre de los niños a ser evaluados'],
                'topic_thematic_referentes' => ['label' => 'Proceso Pedagógico', 'placeholder' => 'Proceso Pedagógico'],
                'ODS_sistematizacion' => ['label' => 'ODS/Observaciones', 'placeholder' => 'ODS/Observaciones'],
            ],
        ],
        'EDUCACION PRIMARIA' => [
            'activity' => [
                'topic_thematic_referentes' => ['label' => 'Contenido (Tema · Tejido · Referentes)', 'placeholder' => 'Contenido (Tema · Tejido · Referentes)'],
            ],
        ],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('activity_field_labels')) {
            Schema::create('activity_field_labels', function (Blueprint $table) {
                $table->id();
                // int unsigned: iguala a `peducativos.id` (bigint rompería el
                // FK con errno 150 en MySQL).
                $table->unsignedInteger('peducativo_id');
                $table->string('model', 20); // 'activity' | 'achievement'
                $table->string('field', 60);
                $table->string('label', 255);
                $table->string('placeholder', 255)->nullable();
                $table->timestamps();

                $table->unique(['peducativo_id', 'model', 'field'], 'af_labels_ped_model_field_unique');
                $table->index(['model', 'field'], 'af_labels_model_field_index');

                $table->foreign('peducativo_id', 'af_labels_peducativo_fk')
                    ->references('id')->on('peducativos')
                    ->cascadeOnDelete();
            });
        }

        $this->seedCurrentLabels();
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_field_labels');
    }

    /**
     * Puebla una fila por (peducativo × model × field) con los labels
     * vigentes: base común + overrides del programa por nombre.
     * Idempotente: usa insertOrIgnore + no duplica si ya existen.
     */
    private function seedCurrentLabels(): void
    {
        if (! Schema::hasTable('activity_field_labels')) {
            return;
        }

        $peducativos = Schema::hasTable('peducativos')
            ? DB::table('peducativos')->select('id', 'name')->get()
            : collect();

        if ($peducativos->isEmpty()) {
            $peducativos = collect([
                (object) ['id' => 1, 'name' => 'EDUCACION INICIAL'],
                (object) ['id' => 2, 'name' => 'EDUCACION PRIMARIA'],
                (object) ['id' => 3, 'name' => 'EDUCACION MEDIA GENERAL'],
            ]);
        }

        // Mapa base "modelo.campo" => [label, placeholder].
        $base = [];
        foreach (self::ACTIVITY_LABELS as $field => $label) {
            $base["activity.{$field}"] = ['label' => $label, 'placeholder' => null];
        }
        foreach (self::ACHIEVEMENT_LABELS as $field => $label) {
            $base["achievement.{$field}"] = ['label' => $label, 'placeholder' => null];
        }

        $now = now()->toDateTimeString();
        $rows = [];

        foreach ($peducativos as $peducativo) {
            $map = $base;

            foreach (self::PEDUCATIVO_OVERRIDES[$peducativo->name] ?? [] as $model => $fields) {
                foreach ($fields as $field => $override) {
                    $map["{$model}.{$field}"] = $override;
                }
            }

            foreach ($map as $key => $item) {
                [$model, $field] = explode('.', $key, 2);
                $rows[] = [
                    'peducativo_id' => $peducativo->id,
                    'model' => $model,
                    'field' => $field,
                    'label' => $item['label'],
                    'placeholder' => $item['placeholder'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('activity_field_labels')->insertOrIgnore($chunk);
        }
    }
};
