<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Estudiant;

class DiagnosticsSeeder extends Seeder
{
    public function run()
    {
        $pensums = Pensum::all();
        $estudiant = Estudiant::first(); // Usa el primer estudiante para las sesiones

        foreach ($pensums as $pensum) {
            for ($i = 1; $i <= 3; $i++) {
                // Crea la pregunta
                $question = DiagQuestion::create([
                    'pensum_id'     => $pensum->id,
                    'pregunta'      => "Pregunta {$i} para pensum {$pensum->id}",
                    'tipo_pregunta' => 'multiple',
                    'orden'         => $i,
                    'difficulty'    => ['easy', 'medium', 'hard'][($i - 1) % 3],
                    'weighing'      => rand(10, 30),
                    'activo'        => true,
                ]);

                // Opciones para pregunta múltiple
                for ($j = 1; $j <= 4; $j++) {
                    DiagOption::create([
                        'question_id' => $question->id,
                        'opcion'      => "Opción {$j}",
                        'valor'       => $j == 1 ? 1 : 0, // Solo la primera es correcta
                    ]);
                }

                // Crea una sesión de diagnóstico
                $session = DiagSession::create([
                    'estudiant_id'    => $estudiant ? $estudiant->id : null,
                    'pensum_id'       => $pensum->id,
                    'iniciado_at'     => now(),
                    'completado_at'   => now()->addMinutes(10),
                    'progreso'        => 100,
                    'total_preguntas' => 3,
                    'activo'          => false,
                ]);

                // Crea una respuesta asociada
                DiagAnswer::create([
                    'question_id' => $question->id,
                    'estudiant_id' => $estudiant ? $estudiant->id : null,
                    'session_id'  => $session->id,
                    'respuesta'   => 'Respuesta ejemplo',
                    'valor_numerico'       => 1,
                ]);
            }
        }
    }
}
