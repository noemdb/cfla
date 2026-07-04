<?php
// /home/nuser/code/s2526/app/Http/Livewire/Academico/Diagnostic/IndexComponent.php

namespace App\Http\Livewire\Academico\Diagnostic;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class IndexComponent extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap-4';

    // Propiedades públicas
    public $isLoading = false;
    public $activeTab = 'dashboard';
    public $search = '';
    public $activeOnly = true;
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    
    // Filtros
    public $filterGradoId = '';
    public $filterDiagMainId = '';
    public $filterSeccionId = '';
    
    // Datos de listas
    public $list_grados;
    public $list_secciones = [];

    // Para modales
    public $showDiagnosticDetails = false;
    public $selectedDiagnostic = null;
    public $selectedDiagnosticStats = [];

    public $showSessionDetails = false;
    public $selectedSession = null;

    // UI Trigger
    public $ui_hash;

    public function startLoading()
    {
        $this->isLoading = true;
    }

    public function stopLoading()
    {
        $this->isLoading = false;
        $this->ui_hash = uniqid();
    }

    public function mount()
    {
        $this->list_grados = Grado::list_pestudio_grado();
    }

    public function updatedFilterGradoId()
    {
        $this->startLoading();
        $this->resetPage();
        
        $this->filterSeccionId = '';
        $this->list_secciones = [];

        if ($this->filterGradoId) {
            $this->list_secciones = Seccion::where('grado_id', $this->filterGradoId)
                ->where('status_active', true)
                ->get();
        }
        
        $this->stopLoading();
    }

    public function setActiveTab($tab)
    {
        $this->startLoading();
        $this->activeTab = $tab;
        $this->resetPage();
        $this->stopLoading();
    }

    public function resetFilters()
    {
        $this->startLoading();
        
        $this->search = '';
        $this->activeOnly = true;
        $this->filterGradoId = '';
        $this->filterDiagMainId = '';
        $this->filterSeccionId = '';
        $this->list_secciones = [];
        $this->sortBy = 'name';
        $this->sortDirection = 'asc';
        
        $this->resetPage();
        $this->stopLoading();
    }

    public function sortBy($field)
    {
        $this->startLoading();
        
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        
        $this->stopLoading();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedActiveOnly()
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->startLoading();
        
        try {
            $diagnostics = DiagMain::with(['sessions', 'questions'])
                ->when($this->search, function ($query) {
                    return $query->where('name', 'like', "%{$this->search}%")
                                 ->orWhere('description', 'like', "%{$this->search}%");
                })
                ->when($this->activeOnly, function ($query) {
                    return $query->where('active', true);
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);

            $stats = $this->getGeneralStats();
            $grados = Grado::where('status_active', true)->get();
            $diagMains = DiagMain::all();

            $this->stopLoading();
            
            return view('livewire.academico.diagnostic.index-component', [
                'diagnostics' => $diagnostics,
                'stats' => $stats,
                'grados' => $grados,
                'diagMains' => $diagMains,
                'diagMainCurrent' => DiagMain::find($this->filterDiagMainId),
            ]);
            
        } catch (Exception $e) {
            Log::error('Error en IndexComponent render: ' . $e->getMessage());
            $this->stopLoading();
            throw $e;
        }
    }

    private function getGeneralStats()
    {
        try {
            $totalDiagnostics = DiagMain::count();
            $activeDiagnostics = DiagMain::where('active', true)->count();
            
            $totalSessions = DiagSession::count();
            $completedSessions = DiagSession::whereNotNull('completado_at')->count();
            
            $totalStudents = DiagSession::distinct('estudiant_id')->count('estudiant_id');
            
            $totalQuestions = DiagQuestion::count();
            $activeQuestions = DiagQuestion::where('activo', true)->count();
            
            return [
                'total_diagnostics' => $totalDiagnostics,
                'active_diagnostics' => $activeDiagnostics,
                'total_sessions' => $totalSessions,
                'completed_sessions' => $completedSessions,
                'completion_rate' => $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100, 2) : 0,
                'unique_students' => $totalStudents,
                'total_questions' => $totalQuestions,
                'active_questions' => $activeQuestions,
            ];
        } catch (Exception $e) {
            Log::error('Error en getGeneralStats: ' . $e->getMessage());
            return [
                'total_diagnostics' => 0,
                'active_diagnostics' => 0,
                'total_sessions' => 0,
                'completed_sessions' => 0,
                'completion_rate' => 0,
                'unique_students' => 0,
                'total_questions' => 0,
                'active_questions' => 0,
            ];
        }
    }

    public function showDiagnosticDetails($diagnosticId)
    {
        $this->startLoading();
        
        try {
            $this->selectedDiagnostic = DiagMain::with([
                'questions',
                'questions.pensum',
                'sessions',
                'sessions.estudiant',
                'referent'
            ])->find($diagnosticId);

            if ($this->selectedDiagnostic) {
                $this->selectedDiagnosticStats = [
                    'total_sessions' => $this->selectedDiagnostic->sessions->count(),
                    'completed_sessions' => $this->selectedDiagnostic->sessions->whereNotNull('completado_at')->count(),
                    'total_questions' => $this->selectedDiagnostic->questions->count(),
                    'active_questions' => $this->selectedDiagnostic->questions->where('activo', true)->count(),
                    'unique_students' => $this->selectedDiagnostic->sessions->unique('estudiant_id')->count(),
                    'completion_rate' => $this->selectedDiagnostic->sessions->count() > 0 
                        ? round(($this->selectedDiagnostic->sessions->whereNotNull('completado_at')->count() / 
                               $this->selectedDiagnostic->sessions->count()) * 100, 2)
                        : 0,
                ];
            }

            $this->showDiagnosticDetails = true;
        } catch (Exception $e) {
            Log::error('Error en showDiagnosticDetails: ' . $e->getMessage());
        }
        
        $this->stopLoading();
    }

    public function closeDiagnosticDetails()
    {
        $this->showDiagnosticDetails = false;
        $this->selectedDiagnostic = null;
        $this->selectedDiagnosticStats = [];
    }

    public function toggleActive($diagnosticId)
    {
        $this->startLoading();
        
        try {
            $diagnostic = DiagMain::find($diagnosticId);
            if ($diagnostic) {
                $diagnostic->update(['active' => !$diagnostic->active]);
            }
        } catch (Exception $e) {
            Log::error('Error en toggleActive: ' . $e->getMessage());
        }
        
        $this->stopLoading();
    }

    public function showSessionDetails($sessionId)
    {
        $this->startLoading();
        
        try {
            $this->selectedSession = DiagSession::with(['estudiant', 'diagMain', 'answers'])
                ->find($sessionId);
            $this->showSessionDetails = true;
        } catch (Exception $e) {
            Log::error('Error en showSessionDetails: ' . $e->getMessage());
        }
        
        $this->stopLoading();
    }

    public function closeSessionDetails()
    {
        $this->showSessionDetails = false;
        $this->selectedSession = null;
    }
}