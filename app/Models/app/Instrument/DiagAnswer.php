<?php

namespace App\Models\app\Instrument;

use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class DiagAnswer extends Model
{
    protected $table = 'diag_answers';

    protected $fillable = [
        'estudiant_id',
        'question_id',
        'option_id',
        'session_id',
        'respuesta',
        'valor_numerico',
        'completado_at',
    ];

    public function question()
    {
        return $this->belongsTo(DiagQuestion::class, 'question_id');
    }

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(DiagOption::class, 'option_id');
    }

    public function isCorrect()
    {
        // Si no hay option_id, no es correcta
        if (!$this->option_id) {
            return false;
        }

        // Cargar la relación si no está cargada
        if (!$this->relationLoaded('selectedOption')) {
            $this->load('selectedOption');
        }

        // Si no hay opción seleccionada después de cargar, no es correcta
        if (!$this->selectedOption) {
            return false;
        }

        // La respuesta es correcta si la opción seleccionada tiene valor = 1
        return $this->selectedOption->valor == 1;
    }

    /**
     * Calculate student precision for answering multiple choice questions correctly
     * Formula: (100 * correct_answers / total_answered_questions)
     *
     * @param int|null $estudiantId - Student ID (optional, uses current answer's student if not provided)
     * @param int|null $pensumId - Pensum ID to filter by (optional)
     * @return array - Returns array with precision percentage, correct answers count, and total answered count
     */
    public static function calculateStudentPrecision($estudiantId = null, $pensumId = null)
    {
        // Build the base query for multiple choice questions only
        $query = self::with(['selectedOption', 'question'])
            ->whereNotNull('completado_at')
            ->whereNotNull('option_id')
            ->whereHas('question', function ($q) {
                $q->where('activo', 1)
                    ->where('tipo_pregunta', 'multiple'); // Only multiple choice questions
            });

        // Filter by student if provided
        if ($estudiantId) {
            $query->where('estudiant_id', $estudiantId);
        }

        // Filter by pensum if provided
        if ($pensumId) {
            $query->whereHas('question', function ($q) use ($pensumId) {
                $q->where('pensum_id', $pensumId);
            });
        }

        $answers = $query->get();

        // Count total answered multiple choice questions
        $totalAnswered = $answers->count();

        if ($totalAnswered == 0) {
            return [
                'precision' => 0,
                'correct_answers' => 0,
                'total_answered' => 0
            ];
        }

        // Count correct answers
        $correctAnswers = $answers->filter(function ($answer) {
            return $answer->isCorrect();
        })->count();

        // Calculate precision using the required formula: (100 * correctas / total_preguntas_contestadas)
        $precision = round((100 * $correctAnswers) / $totalAnswered, 2);

        return [
            'precision' => $precision,
            'correct_answers' => $correctAnswers,
            'total_answered' => $totalAnswered
        ];
    }

    /**
     * Get precision statistics for a specific student
     *
     * @param int $estudiantId - Student ID
     * @param int|null $pensumId - Optional pensum filter
     * @return array
     */
    public static function getStudentPrecisionStats($estudiantId, $pensumId = null)
    {
        return self::calculateStudentPrecision($estudiantId, $pensumId);
    }

    /**
     * Get overall precision statistics for all students
     *
     * @param int|null $pensumId - Optional pensum filter
     * @return array
     */
    public static function getOverallPrecisionStats($pensumId = null)
    {
        return self::calculateStudentPrecision(null, $pensumId);
    }
}
