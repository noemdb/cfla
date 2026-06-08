<?php

namespace App\Http\Livewire\ActivityLogs;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class SystemLogs extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-5';

    public $selected_date;
    public $level = '';
    public $search = '';
    public $sortField = 'date';
    public $sortDirection = 'desc';
    public $showDetails = false;
    public $selectedLog = null;

    protected $queryString = [
        'selected_date' => ['except' => ''],
        'level' => ['except' => ''],
        'search' => ['except' => ''],
        'sortField' => ['except' => 'date'],
        'sortDirection' => ['except' => 'desc'],
        'page' => ['except' => 1],
    ];

    protected $listeners = [
        'refreshSystemLogsData' => 'getStats'
    ];

    public function mount()
    {
        $this->selected_date = now()->format('Y-m-d');
    }

    public function getStats()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function showDetails($logId)
    {
        $this->selectedLog = $logId;
        $this->showDetails = true;
    }

    public function closeDetails()
    {
        $this->showDetails = false;
        $this->selectedLog = null;
    }

    public function getLogLevelClass($level)
    {
        return match (strtolower($level)) {
            'emergency', 'alert', 'critical', 'error' => 'danger',
            'warning' => 'warning',
            'notice', 'info' => 'info',
            'debug' => 'secondary',
            default => 'primary'
        };
    }

    public function render()
    {
        $logs = collect();
        $logPath = storage_path('logs');

        // Obtener el archivo de log para la fecha seleccionada
        $logFile = $logPath . '/laravel-' . $this->selected_date . '.log';

        if (File::exists($logFile)) {
            $content = File::get($logFile);
            $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?\.(\w+): (.*?)(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|$)/s';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $date = $match[1];
                $level = strtolower($match[2]);
                $message = trim($match[3]);

                if ($this->level && strtolower($this->level) !== $level) {
                    continue;
                }

                if ($this->search && !str_contains(strtolower($message), strtolower($this->search))) {
                    continue;
                }

                $logs->push([
                    'id' => md5($date . $message),
                    'date' => $date,
                    'level' => $level,
                    'message' => $message,
                    'channel' => 'laravel'
                ]);
            }
        }

        // Ordenar la colección
        $logs = $logs->sortBy(function ($log) {
            return $log[$this->sortField];
        }, SORT_REGULAR, $this->sortDirection === 'desc');

        // Convertir a paginación
        $page = $this->page ?? 1;
        $perPage = 25;
        $logs = new \Illuminate\Pagination\LengthAwarePaginator(
            $logs->forPage($page, $perPage),
            $logs->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.activity-logs.system-logs', [
            'logs' => $logs,
            'levels' => [
                'emergency' => 'Emergency',
                'alert' => 'Alert',
                'critical' => 'Critical',
                'error' => 'Error',
                'warning' => 'Warning',
                'notice' => 'Notice',
                'info' => 'Info',
                'debug' => 'Debug'
            ]
        ]);
    }
}
