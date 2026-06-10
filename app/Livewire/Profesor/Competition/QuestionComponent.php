<?php

namespace App\Livewire\Profesor\Competition;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Profesor;
use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class QuestionComponent extends Component
{
    use WireUiActions, WithFileUploads, WithPagination;

    public $pensum;
    public $profesor;
    public $pensumId;

    public QuestionForm $questionForm;
    public $debates;
    public $categories = [];
    public $categoriesWithCounts = [];
    public $listGrado;
    public $selectedCategory = '';
    public $showFormModal = false;
    public $editId = null;
    public $showDetailModal = false;
    public $detailQuestion = null;
    public $showOptionsModal = false;
    public $optionQuestionId = null;
    public $optionForm = [];
    public $editOptionId = null;

    // Filters & Pagination
    public $search = '';
    public $filterDebate = '';
    public $filterStatus = '';
    public $paginate = 5;

    // Attachment
    public $attachmentUpload;
    public $existingAttachment;

    public function mount($pensumId)
    {
        $user = Auth::user();
        $this->profesor = Profesor::where('user_id', $user->id)->first();
        $this->pensum = Pensum::with(['asignatura', 'grado.pestudio'])->findOrFail($pensumId);
        $this->pensumId = $pensumId;

        // Debates disponibles para el pensum (a través del grado)
        $this->debates = Debate::where('grado_id', $this->pensum->grado_id)
            ->orderBy('name')
            ->get();

        // Categorías filtradas por pestudio
        $this->categories = $this->getFilteredCategories();

        // Grados del profesor
        $this->listGrado = ($this->profesor)
            ? Profesor::list_grado($this->profesor->id)
            : collect();

        $this->resetQuestion();
    }

    /**
     * Calcula el conteo de preguntas por categoría para el pensum actual.
     * categories es un array asociativo: ['[31059] Castellano' => 'Castellano', ...].
     */
    protected function getCategoriesWithCounts()
    {
        $categories = $this->categories ?? [];

        if (empty($categories)) {
            return [];
        }

        $counts = DebateQuestion::where('pensum_id', $this->pensumId)
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return collect($categories)->map(function ($shortName, $fullKey) use ($counts) {
            return [
                'key' => $fullKey,
                'name' => $shortName,
                'count' => $counts[$fullKey] ?? 0,
            ];
        })->values()->toArray();
    }

    /**
     * Obtiene categorías filtradas según el pestudio del grado del pensum.
     * Retorna un array asociativo con el formato:
     *   ['[31059] Castellano' => 'Castellano', '[31059] Inglés' => 'Inglés', ...]
     */
    protected function getFilteredCategories()
    {
        $pestudio = $this->pensum->grado?->pestudio;
        if (!$pestudio) {
            return [];
        }

        // Normalizar: si el código trae corchetes (ej: [21000]) los limpiamos
        $pestudioCode = trim($pestudio->code, '[]');

        return collect(DebateQuestion::CATEGORY)
            ->filter(function ($name, $key) use ($pestudioCode) {
                return str_starts_with($key, '[' . $pestudioCode . ']');
            })
            ->toArray();
    }

    // ─── FILTER RESET PAGE ───────────────────────────────────────

    public function updatingSearch() { $this->resetPage(); }

    public function updatingFilterDebate() { $this->resetPage(); }

    public function updatingFilterStatus() { $this->resetPage(); }

    public function updatingSelectedCategory() { $this->resetPage(); }

    public function updatingPaginate() { $this->resetPage(); }

    public function resetQuestion()
    {
        $this->questionForm->reset();
        $this->attachmentUpload = null;
        $this->existingAttachment = null;
    }

    public function setCreate()
    {
        $this->resetQuestion();
        $this->editId = null;
        $this->showFormModal = true;
    }

    public function setEdit($id)
    {
        $q = DebateQuestion::findOrFail($id);
        $this->editId = $id;
        $this->questionForm->fillFromModel($q);
        $this->existingAttachment = $q->attachment;
        $this->showFormModal = true;
    }

    public function filterByCategory($category)
    {
        $this->selectedCategory = $category;
    }

    public function setShowDetail($id)
    {
        $this->detailQuestion = DebateQuestion::with([
            'debate',
            'options',
            'user',
            'pensum.asignatura',
            'pensum.grado.pestudio',
        ])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->detailQuestion = null;
    }

    public function backToIndex()
    {
        $this->showFormModal = false;
        $this->editId = null;
        $this->resetQuestion();
    }

    public function save()
    {
        $this->questionForm->validate();

        $data = [
            'debate_id' => $this->questionForm->debate_id,
            'pensum_id' => $this->pensumId,
            'category' => $this->questionForm->category,
            'text' => $this->questionForm->text,
            'time' => $this->questionForm->time ?? 30,
            'weighting' => $this->questionForm->weighting ?? 1,
            'observation' => ($this->questionForm->observation === '') ? null : $this->questionForm->observation,
            'status_active' => $this->questionForm->status_active ?? true,
        ];

        // Handle attachment upload
        if ($this->attachmentUpload) {
            $this->validate([
                'attachmentUpload' => 'nullable|image|max:1024',
            ]);
            $filename = Str::random(20) . '.' . $this->attachmentUpload->extension();
            $this->attachmentUpload->storeAs('educationals/competitions', $filename, 'public');
            $data['attachment'] = 'competitions/' . $filename;
        } elseif ($this->editId && $this->existingAttachment) {
            $data['attachment'] = $this->existingAttachment;
        }

        $data['user_id'] = Auth::id();

        if ($this->editId) {
            $q = DebateQuestion::findOrFail($this->editId);
            $q->update($data);
            $this->notification()->success(
                '¡Excelente!',
                'Pregunta actualizada exitosamente'
            );
        } else {
            DebateQuestion::create($data);
            $this->notification()->success(
                '¡Excelente!',
                'Pregunta registrada exitosamente'
            );
        }

        $this->backToIndex();
    }

    public function questionDelete($id)
    {
        $question = DebateQuestion::find($id);
        if (!$question) {
            return;
        }

        // Solo el creador puede eliminar
        if ($question->user_id !== Auth::id()) {
            $this->notification()->error(
                '¡Error!',
                'No tienes permiso para eliminar esta pregunta'
            );
            return;
        }

        $question->delete();
        $this->notification()->success(
            '¡Excelente!',
            'Pregunta eliminada exitosamente'
        );
    }

    public function toggleActive($id)
    {
        $question = DebateQuestion::find($id);
        if (!$question) {
            return;
        }

        $question->update(['status_active' => !$question->status_active]);

        $this->notification()->success(
            '¡Excelente!',
            $question->status_active
                ? 'Pregunta activada exitosamente'
                : 'Pregunta desactivada exitosamente'
        );
    }

    // ─── OPTIONS MANAGEMENT ──────────────────────────────────────

    public function manageOptions($id)
    {
        $this->optionQuestionId = $id;
        $this->resetOptionForm();
        $this->editOptionId = null;
        $this->showOptionsModal = true;
    }

    public function closeOptions()
    {
        $this->showOptionsModal = false;
        $this->optionQuestionId = null;
        $this->editOptionId = null;
        $this->resetOptionForm();
    }

    public function resetOptionForm()
    {
        $this->optionForm = [
            'text' => '',
            'observation' => '',
            'status_option_correct' => false,
            'status_wrong_answer' => false,
        ];
    }

    public function setCreateOption()
    {
        $this->resetOptionForm();
        $this->editOptionId = null;
    }

    public function setEditOption($id)
    {
        $option = DebateOption::findOrFail($id);
        $this->editOptionId = $id;
        $this->optionForm = [
            'text' => $option->text,
            'observation' => $option->observation ?? '',
            'status_option_correct' => $option->status_option_correct,
            'status_wrong_answer' => $option->status_wrong_answer,
        ];
    }

    public function saveOption()
    {
        $this->validate([
            'optionForm.text' => 'required|string|max:65535',
            'optionForm.observation' => 'nullable|string|max:65535',
            'optionForm.status_option_correct' => 'nullable|boolean',
            'optionForm.status_wrong_answer' => 'nullable|boolean',
        ]);

        $data = [
            'question_id' => $this->optionQuestionId,
            'text' => $this->optionForm['text'],
            'observation' => ($this->optionForm['observation'] === '') ? null : $this->optionForm['observation'],
            'status_option_correct' => $this->optionForm['status_option_correct'] ?? false,
            'status_wrong_answer' => $this->optionForm['status_wrong_answer'] ?? false,
        ];

        if ($this->editOptionId) {
            $option = DebateOption::findOrFail($this->editOptionId);
            $option->update($data);
            $this->notification()->success('¡Excelente!', 'Opción actualizada exitosamente');
        } else {
            DebateOption::create($data);
            $this->notification()->success('¡Excelente!', 'Opción registrada exitosamente');
        }

        $this->setCreateOption();
    }

    public function deleteOption($id)
    {
        $option = DebateOption::find($id);
        if (!$option) {
            return;
        }

        if ($option->question_id !== $this->optionQuestionId) {
            return;
        }

        $option->delete();
        $this->notification()->success('¡Excelente!', 'Opción eliminada exitosamente');
    }

    public function toggleOptionCorrect($id)
    {
        $option = DebateOption::findOrFail($id);
        // If setting this one as correct, unset all others for the same question
        if (!$option->status_option_correct) {
            DebateOption::where('question_id', $option->question_id)
                ->update(['status_option_correct' => false]);
        }
        $option->update(['status_option_correct' => !$option->status_option_correct]);
    }

    public function render()
    {
        $query = DebateQuestion::where('pensum_id', $this->pensumId);

        // Filtro: categoría
        if ($this->selectedCategory) {
            $query->where('category', $this->selectedCategory);
        }

        // Filtro: debate
        if ($this->filterDebate) {
            $query->where('debate_id', $this->filterDebate);
        }

        // Filtro: estado activo/inactivo
        if ($this->filterStatus === 'active') {
            $query->where('status_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('status_active', false);
        }

        // Filtro: búsqueda por texto
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('text', 'like', '%' . $this->search . '%')
                  ->orWhere('observation', 'like', '%' . $this->search . '%');
            });
        }

        $questions = $query->orderBy('created_at', 'desc')
            ->paginate($this->paginate);

        $this->categoriesWithCounts = $this->getCategoriesWithCounts();

        return view('livewire.profesor.competition.question-component', [
            'questions' => $questions,
        ]);
    }
}
