<?php

namespace App\Http\Livewire\Diagnostic;

use App\Models\app\Instrument\DiagReport;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DiagReportIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $statusFilter = '';
    public $lapsoFilter = '';

    protected $queryString = ['search', 'statusFilter', 'lapsoFilter'];

    public function mount()
    {
        $this->authorize('viewAny', DiagReport::class);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = DiagReport::with(['estudiant', 'diagMain', 'lapso', 'results'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->search) {
            $query->whereHas('estudiant', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->lapsoFilter) {
            $query->where('lapso_id', $this->lapsoFilter);
        }

        // Role-based filtering
        $user = auth()->user();
        if ($user->isProfesor() && !$user->isControl() && !$user->IsDirector()) {
            // Docentes only see their students
            $query->whereHas('estudiant.inscripcions.pensum.pevaluacions', function ($q) use ($user) {
                $q->where('profesor_id', $user->profesor->id ?? 0);
            });
        }

        $reports = $query->paginate(15);

        return view('livewire.diagnostic.diag-report-index', [
            'reports' => $reports,
        ]);
    }
}
