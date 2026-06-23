<?php

namespace App\Models\app\Educational;

use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Collection;

trait IndicatorTrait
{
    public function getAccuracyForGrado($gradoId): object
    {
        $debates = $this->debates()
            ->where('grado_id', $gradoId)
            ->with([
                'questions' => function ($q) {
                    $q->whereColumn('time', 'time_elapsed')
                        ->where('time', '>', 0);
                },
                'questions.answers' => function ($q) {
                    $q->where('score', '>', 0);
                }
            ])
            ->get();

        $totalScore = 0;
        $maxPossibleScore = 0;
        $validQuestionsCount = 0;
        $correctAnswersCount = 0;

        foreach ($debates as $debate) {
            foreach ($debate->questions as $question) {
                $validQuestionsCount++;
                foreach ($question->answers as $answer) {
                    $totalScore += $answer->score;
                    $correctAnswersCount++;
                }
                $maxPossibleScore += $question->weighting;
            }
        }

        $accuracy = $validQuestionsCount > 0 ? round(($correctAnswersCount / $validQuestionsCount) * 100, 2) : 0;

        return (object) [
            'gradoId' => $gradoId,
            'totalScore' => $totalScore,
            'maxPossibleScore' => $maxPossibleScore,
            'accuracy' => $accuracy,
            'validQuestionsCount' => $validQuestionsCount,
            'correctAnswersCount' => $correctAnswersCount,
        ];
    }

    public function getWrongAnswerForGrado($gradoId): object
    {
        $debates = $this->debates()
            ->where('grado_id', $gradoId)
            ->with([
                'questions' => function ($q) {
                    $q->whereColumn('time', 'time_elapsed')
                        ->where('time', '>', 0);
                },
                'questions.answers' => function ($q) use ($gradoId) {
                    $q->where('grado_id', $gradoId)
                        ->where('score', '>', 0);
                }
            ])
            ->get();

        $validQuestionsCount = 0;
        $wrongAnswersCount = 0;

        foreach ($debates as $debate) {
            foreach ($debate->questions as $question) {
                $validQuestionsCount++;

                $answer = $question->answers->first(); // una sola por grado
                if (!$answer) {
                    $wrongAnswersCount++;
                }
            }
        }

        $wrongPercentage = $validQuestionsCount > 0
            ? round(($wrongAnswersCount / $validQuestionsCount) * 100, 2)
            : 0;

        return (object) [
            'gradoId' => $gradoId,
            'wrongAnswersCount' => $wrongAnswersCount,
            'validQuestionsCount' => $validQuestionsCount,
            'wrongPercentage' => $wrongPercentage,
        ];
    }

    public function getCorrectAnsweredQuestionsByGrado($gradoId)
    {
        return DebateQuestion::query()
            ->whereHas('debate', function ($query) use ($gradoId) {
                $query->where('grado_id', $gradoId);
            })
            ->whereColumn('time', 'time_elapsed')
            ->where('time', '>', 0)
            ->whereHas('answers', function ($query) {
                $query->where('score', '>', 0);
            })
            ->with([
                'debate.grado:id,name',
                'pensum.asignatura:id,name',
                'answers' => function ($query) {
                    $query->where('score', '>', 0);
                }
            ])
            ->get();
    }

    public function getWrongAnsweredQuestionsByGrado($gradoId): Collection
    {
        return DebateQuestion::query()
            ->whereHas('debate', function ($query) use ($gradoId) {
                $query->where('grado_id', $gradoId);
            })
            ->whereColumn('time', 'time_elapsed')
            ->where('time', '>', 0)
            ->whereDoesntHave('answers', function ($query) use ($gradoId) {
                $query->where('grado_id', $gradoId)
                    ->where('score', '>', 0);
            })
            ->with([
                'debate.grado:id,name',
                'pensum.asignatura:id,name'
            ])
            ->get();
    }

    public function getAnsweredQuestionsByGradoId($gradoId)
    {
        return DebateQuestion::query()
            ->whereHas('debate', function ($query) use ($gradoId) {
                $query->where('grado_id', $gradoId);
            })
            ->whereColumn('time', 'time_elapsed')
            ->where('time', '>=', 0)
            ->with([
                'debate.grado:id,name',
                'pensum.asignatura:id,name',
                'answers' => function ($query) {
                    $query->where('score', '>', 0);
                }
            ])
            ->get();
    }

    public function getAnsweredQuestionsCountByGradoId($gradoId)
    {
        return $this->getAnsweredQuestionsByGradoId($gradoId)->count();
    }

    public function getTotalWeightingByGradoId($gradoId)
    {
        return $this->getAnsweredQuestionsByGradoId($gradoId)->sum('weighting');
    }

    public function getAnsweredQuestionsCountBySeccionId($seccionId)
    {
        $seccion = Seccion::where('id', $seccionId)->first();
        $grado = $seccion->grado ?? null;
        $seccions = $grado->getSeccionsActive() ?? null;
        $countSeccion = $seccions->count() ?? null;
        $countQuestion = $this->getAnsweredQuestionsByGradoId($grado->id)->count();
        return ($countSeccion) ? $countQuestion / $countSeccion : null;
    }

    public function getTotalWeightingBySeccionId($seccionId)
    {
        $seccion = Seccion::where('id', $seccionId)->first();
        $grado = $seccion->grado ?? null;
        $seccions = $grado->getSeccionsActive() ?? null;
        $countSeccion = $seccions->count() ?? null;
        $countQuestion = $this->getTotalWeightingByGradoId($grado->id) ?? null; //dd($countSeccion,$countQuestion);
        return ($countSeccion) ? $countQuestion / $countSeccion : null;
    }

    public function getAccuracyForSeccion($seccionId)
    {
        $totalWeighting = $this->getTotalWeightingBySeccionId($seccionId);
        $totalScore = $this->getTotalScoreForSection($seccionId);
        return ($totalWeighting) ? round(($totalScore / $totalWeighting) * 100, 2) : null;
    }

    public function getAccuracyForQuestionCategory($category): object
    {
        $debates = $this->debates()
            ->with([
                'questions' => function ($q) use ($category) {
                    $q->whereColumn('time', 'time_elapsed')
                        ->where('time', '>', 0)
                        ->where('category', $category)
                        ->where('weighting', '>', 0);
                },
                'questions.answers' => function ($q) {
                    $q->where('score', '>', 0);
                }
            ])
            ->get();

        $totalScore = 0;
        $maxPossibleScore = 0;
        $validQuestionsCount = 0;
        $correctAnswersCount = 0;

        foreach ($debates as $debate) {
            foreach ($debate->questions as $question) {
                $validQuestionsCount++;
                foreach ($question->answers as $answer) {
                    $totalScore += $answer->score;
                    $correctAnswersCount++;
                }
                $maxPossibleScore += $question->weighting;
            }
        }

        $accuracy = $validQuestionsCount > 0
            ? round(($correctAnswersCount / $validQuestionsCount) * 100, 2)
            : 0;

        return (object) [
            'totalScore' => $totalScore,
            'maxPossibleScore' => $maxPossibleScore,
            'accuracy' => $accuracy,
            'validQuestionsCount' => $validQuestionsCount,
            'correctAnswersCount' => $correctAnswersCount,
        ];
    }

    public function getAccuracyForQuestionCategoryAll($gradoId = null): object
    {
        $query = $this->debates()->with([
            'questions' => function ($q) {
                $q->whereColumn('time', 'time_elapsed')
                    ->where('time', '>', 0)
                    ->where('weighting', '>', 0)
                    ->with('answers');
            }
        ]);

        if ($gradoId) {
            $query->where('grado_id', $gradoId);
        }

        $debates = $query->get();

        $statsByCategory = [];

        foreach ($debates as $debate) {
            foreach ($debate->questions as $question) {
                if (!$question->category) continue; // seguridad

                $categoryName = $question->category;

                if (!isset($statsByCategory[$categoryName])) {
                    $statsByCategory[$categoryName] = [
                        'category' => $categoryName,
                        'totalScore' => 0,
                        'maxPossibleScore' => 0,
                        'validQuestionsCount' => 0,
                        'correctAnswersCount' => 0,
                    ];
                }

                $statsByCategory[$categoryName]['validQuestionsCount']++;
                $statsByCategory[$categoryName]['maxPossibleScore'] += $question->weighting;

                foreach ($question->answers as $answer) {
                    if ($answer->score > 0) {
                        $statsByCategory[$categoryName]['totalScore'] += $answer->score;
                        $statsByCategory[$categoryName]['correctAnswersCount']++;
                    }
                }
            }
        }

        // Calcular accuracy final
        foreach ($statsByCategory as &$data) {
            $data['accuracy'] = $data['validQuestionsCount'] > 0
                ? round(($data['correctAnswersCount'] / $data['validQuestionsCount']) * 100, 2)
                : 0;
        }

        return (object) $statsByCategory;
    }
}
