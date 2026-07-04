<?php

namespace App\Http\Livewire\Diagnostic;

use App\Models\app\Instrument\DiagReportAuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogViewer extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $reportId;
    public $actionFilter = '';
    public $userFilter = '';

    public function mount($reportId = null)
    {
        $this->reportId = $reportId;
    }

    public function render()
    {
        $query = DiagReportAuditLog::with(['user', 'report'])
            ->orderBy('created_at', 'desc');

        if ($this->reportId) {
            $query->where('report_id', $this->reportId);
        }

        if ($this->actionFilter) {
            $query->where('action', $this->actionFilter);
        }

        if ($this->userFilter) {
            $query->where('user_id', $this->userFilter);
        }

        $logs = $query->paginate(20);

        return view('livewire.diagnostic.audit-log-viewer', [
            'logs' => $logs,
        ]);
    }
}
