<?php

namespace App\Services\Diagnostic;

use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use Illuminate\Support\Collection;

/**
 * Resumen de resultados de una sesión de diagnóstico.
 *
 * Una sesión es por (estudiante, área de formación): `Diagnostic::startDiagnostic`
 * le sirve las preguntas **activas de su pensum** y guarda ese número en
 * `diag_sessions.total_preguntas`. El diagnóstico es transversal a many áreas,
 * así que NO sirve como denominador: un DiagMain puede tener cientos de
 * preguntas de todas las áreas y grados, y usarlo como total daba cifras sin
 * sentido (p. ej. 955 preguntas del instrumento − 10 contestadas = "945 sin
 * responder" en una sesión de 10).
 */
class SessionResultsService
{
    /**
     * Totales de una sesión: respondidas, correctas, incorrectas y sin responder.
     *
     * El universo de preguntas es el del área evaluada (no el del diagnóstico);
     * si el área no tiene preguntas activas se recurre al snapshot
     * `total_preguntas` y, en último caso, a lo efectivamente contestado.
     *
     * @return array{total: int, answered: int, correct: int, incorrect: int, unanswered: int, percentage: int|null}
     */
    public function summarize(DiagSession $session): array
    {
        $answers = $session->relationLoaded('answers')
            ? $session->answers
            : $session->answers()->get();

        // Una pregunta contestada cuenta una sola vez, aunque tenga varias filas.
        $answeredIds = $answers->pluck('question_id')->filter()->unique()->values();
        $answered = $answeredIds->count();

        $correct = $answers->filter(fn ($a) => $a->isCorrect())->count();
        $incorrect = $answers->filter(fn ($a) => $a->option_id && ! $a->isCorrect())->count();

        $universe = $this->questionUniverse($session);
        $total = max($universe, $answered);
        $unanswered = max(0, $total - $answered);

        return [
            'total' => $total,
            'answered' => $answered,
            'correct' => $correct,
            'incorrect' => $incorrect,
            'unanswered' => $unanswered,
            'percentage' => $answered > 0 ? (int) round(($correct / $answered) * 100) : null,
        ];
    }

    /**
     * Aciertos de las filas de la sesión (para listados paginados, sin recalcular
     * el universo completo por fila).
     *
     * @param  Collection<int, DiagAnswer>  $answers
     * @return array{answered: int, correct: int, percentage: int|null}
     */
    public function summarizeAnswers(Collection $answers): array
    {
        $answered = $answers->pluck('question_id')->filter()->unique()->count();
        $correct = $answers->filter(fn ($a) => $a->isCorrect())->count();

        return [
            'answered' => $answered,
            'correct' => $correct,
            'percentage' => $answered > 0 ? (int) round(($correct / $answered) * 100) : null,
        ];
    }

    /**
     * Cantidad de preguntas que el área evaluada tenía activas, replicando el
     * criterio de `Diagnostic::startDiagnostic` / `refreshQuestionIds`.
     */
    protected function questionUniverse(DiagSession $session): int
    {
        if (! $session->pensum_id) {
            return 0;
        }

        $active = DiagQuestion::where('pensum_id', $session->pensum_id)
            ->where('activo', true)
            ->count();

        return $active > 0 ? (int) $active : (int) ($session->total_preguntas ?? 0);
    }
}
