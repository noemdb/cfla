<?php

namespace App\Livewire\Admin\Logs;

use App\Livewire\Admin\Logs\Services\LogParser;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WithPagination, WireUiActions;

    public $paginate = 25;

    public $selectedFile = 'laravel.log';
    public $fileList = [];

    public $filterLevel = '';
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';

    public $levelOptions = [
        ''          => 'Todos los niveles',
        'EMERGENCY' => 'EMERGENCY',
        'ALERT'     => 'ALERT',
        'CRITICAL'  => 'CRITICAL',
        'ERROR'     => 'ERROR',
        'WARNING'   => 'WARNING',
        'NOTICE'    => 'NOTICE',
        'INFO'      => 'INFO',
        'DEBUG'     => 'DEBUG',
    ];

    // ─── Maintenance actions (modal-driven) ────────────────────
    public $confirmAction = null; // 'clean' | 'delete'
    public $confirmFileName = null;

    private LogParser $parser;

    public function boot(LogParser $parser)
    {
        $this->parser = $parser;
    }

    public function mount()
    {
        $this->refreshFileList();

        // Fall back to a valid default if the configured file vanished.
        if (! isset($this->fileList[$this->selectedFile])) {
            $this->selectedFile = array_key_first($this->fileList) ?? 'laravel.log';
        }
    }

    public function render()
    {
        $path = storage_path('logs/' . $this->selectedFile);

        if (! is_file($path)) {
            $this->notification()->error(
                title: 'Archivo no encontrado',
                description: 'El archivo de log seleccionado ya no existe.'
            );

            return view('livewire.admin.logs.index-component', [
                'logs'      => collect(),
                'stats'     => ['total' => 0],
                'fileInfo'  => null,
                'fileList'  => $this->fileList,
                'levelOptions' => $this->levelOptions,
                'tooLarge'  => false,
            ]);
        }

        $tooLarge = $this->parser->exceedsSizeLimit($path);

        if ($tooLarge) {
            return view('livewire.admin.logs.index-component', [
                'logs'      => collect(),
                'stats'     => ['total' => 0],
                'fileInfo'  => $this->parser->fileInfo($path),
                'fileList'  => $this->fileList,
                'levelOptions' => $this->levelOptions,
                'tooLarge'  => true,
            ]);
        }

        $parsed = $this->parser->parse(
            $path,
            $this->search,
            $this->filterLevel,
            $this->dateFrom,
            $this->dateTo
        );

        $logs = $this->paginateEntries($parsed);

        // Stats computed on the *filtered* set so admins see real context.
        $stats = $this->parser->stats($parsed);

        return view('livewire.admin.logs.index-component', [
            'logs'      => $logs,
            'stats'     => $stats,
            'fileInfo'  => $this->parser->fileInfo($path),
            'fileList'  => $this->fileList,
            'levelOptions' => $this->levelOptions,
            'tooLarge'  => false,
        ]);
    }

    // ─── Livewire trait handlers ───────────────────────────────
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterLevel() { $this->resetPage(); }
    public function updatingDateFrom() { $this->resetPage(); }
    public function updatingDateTo() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }

    public function updatingSelectedFile()
    {
        $this->resetPage();
        $this->filterLevel = '';
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->refreshFileList();
    }

    // ─── Actions ───────────────────────────────────────────────
    public function selectFile($name)
    {
        if (isset($this->fileList[$name])) {
            $this->selectedFile = $name;
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->filterLevel = '';
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    /**
     * Download the selected file (guarded, whitelisted against fileList).
     */
    public function download()
    {
        if (! isset($this->fileList[$this->selectedFile])) {
            $this->notification()->error('Error', 'Archivo no válido para descargar.');
            return;
        }

        $path = storage_path('logs/' . $this->selectedFile);

        return Response::download($path, $this->selectedFile);
    }

    public function confirmClean() { $this->confirmAction = 'clean'; }
    public function confirmDelete() { $this->confirmAction = 'delete'; }
    public function cancelAction() { $this->confirmAction = null; }

    /**
     * Empty (truncate) the selected log file, keeping the file itself.
     */
    public function cleanLog()
    {
        if ($this->confirmAction !== 'clean' || ! isset($this->fileList[$this->selectedFile])) {
            return;
        }

        $path = storage_path('logs/' . $this->selectedFile);
        File::put($path, '');

        $this->confirmAction = null;
        $this->flushParserCache();
        $this->notification()->success(
            title: 'Log Limpiado',
            description: "El archivo {$this->selectedFile} se vació correctamente.",
            timeout: 3000
        );
    }

    /**
     * Remove the selected log file entirely (after creating a backup copy).
     */
    public function deleteLog()
    {
        if ($this->confirmAction !== 'delete' || ! isset($this->fileList[$this->selectedFile])) {
            return;
        }

        $path = storage_path('logs/' . $this->selectedFile);

        try {
            // Safety net: keep a backup before destroying data.
            $backupDir = storage_path('logs/backups');
            File::ensureDirectoryExists($backupDir);
            $stamp = now()->format('Ymd_His');
            File::copy($path, $backupDir . '/' . $stamp . '_' . $this->selectedFile);

            File::delete($path);

            // Reset selection to an existing file.
            $this->refreshFileList();
            $this->selectedFile = array_key_first($this->fileList) ?? '';

            $this->confirmAction = null;
            $this->flushParserCache();
            $this->notification()->success(
                title: 'Log Eliminado',
                description: "Se eliminó {$this->selectedFile}. Se guardó una copia en logs/backups/.",
                timeout: 4000
            );
        } catch (\Throwable $e) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se pudo eliminar el log: ' . $e->getMessage()
            );
        }
    }

    // ─── Internal helpers ──────────────────────────────────────
    private function paginateEntries(array $entries): LengthAwarePaginator
    {
        $collection = new Collection($entries);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $total = $collection->count();
        $perPage = max(1, (int) $this->paginate);
        $slice = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator($slice, $total, $perPage, $page, [
            'path'  => LengthAwarePaginator::resolveCurrentPath(),
            'query' => request()->query(),
        ]);
    }

    private function refreshFileList()
    {
        $files = $this->parser->files();
        $map = [];

        foreach ($files as $file) {
            $map[$file['name']] = $file;
        }

        $this->fileList = $map;
    }

    private function flushParserCache()
    {
        // Invalidate any cached content/parse of the affected file.
        // The parse key appends filemtime, but we drop both caches defensively.
        $path = storage_path('logs/' . $this->selectedFile);
        $digest = md5($path);

        if (function_exists('cache')) {
            cache()->forget('log-viewer.content.' . $digest);
            // Parse keys are time-stamped; remove the currently known one.
            $stamp = (int) @filemtime($path);
            cache()->forget('log-viewer.parse.' . $digest . '.' . $stamp);
        }
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
