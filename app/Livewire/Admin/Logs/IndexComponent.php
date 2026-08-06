<?php

namespace App\Livewire\Admin\Logs;

use App\Livewire\Admin\Logs\Services\LogParser;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WireUiActions, WithFileUploads, WithPagination;

    public $paginate = 25;

    public $selectedFolder = '';

    public $folderList = [];

    public $selectedFile = 'laravel.log';

    public $fileList = [];

    public $filterLevel = '';

    public $search = '';

    public $searchIncludeStack = false;

    public $autoRefresh = false;

    /**
     * Sort direction of the date column ('desc' = newest first, 'asc' = oldest first).
     */
    public $sortDirection = 'desc';

    /**
     * Entries shown on the current page, used by the detail panel.
     */
    public $pageEntries = [];

    /**
     * Index (within the current page) of the entry in the side detail panel.
     */
    public $selectedIndex = null;

    public $dateFrom = '';

    public $dateTo = '';

    // ─── B4 — drag & drop local ────────────────────────────────
    #[Validate('file|mimes:log,txt,text|max:'.LogParser::SIZE_LIMIT / 1024)]
    public ?TemporaryUploadedFile $uploadedLog = null;

    public string $uploadedLogName = '';

    // ─── B5 — diff entre fechas ────────────────────────────────
    public bool $diffMode = false;

    public string $diffFromA = '';

    public string $diffToA = '';

    public string $diffFromB = '';

    public string $diffToB = '';

    public $levelOptions = [
        '' => 'Todos los niveles',
        'EMERGENCY' => 'EMERGENCY',
        'ALERT' => 'ALERT',
        'CRITICAL' => 'CRITICAL',
        'ERROR' => 'ERROR',
        'WARNING' => 'WARNING',
        'NOTICE' => 'NOTICE',
        'INFO' => 'INFO',
        'DEBUG' => 'DEBUG',
    ];

    // ─── Maintenance actions (modal-driven) ────────────────────
    public $confirmAction = null; // 'clean' | 'delete' | 'deleteAll'

    public $confirmFileName = null;

    public $pruneDays = 30;

    private LogParser $parser;

    public function boot(LogParser $parser)
    {
        $this->parser = $parser;
    }

    public function mount()
    {
        $this->refreshFolderList();
        $this->refreshFileList();

        // Fall back to a valid default if the configured file vanished.
        if ($this->selectedFile === '' || ! isset($this->fileList[$this->selectedFile])) {
            $this->selectedFile = array_key_first($this->fileList) ?? 'laravel.log';
        }
    }

    public function render()
    {
        // B5 — the diff view replaces the regular table entirely.
        if ($this->diffMode) {
            return $this->renderDiff();
        }

        // B4 — when a local .log has been dropped/selected, inspect that instead.
        if ($this->uploadedLog) {
            return $this->renderUploaded();
        }

        $path = $this->resolveSelectedPath();

        if (! is_file($path)) {
            $this->notification()->error(
                title: 'Archivo no encontrado',
                description: 'El archivo de log seleccionado ya no existe.'
            );

            return view('livewire.admin.logs.index-component', [
                'logs' => $this->paginateEntries([]),
                'stats' => ['total' => 0],
                'fileInfo' => null,
                'fileList' => $this->fileList,
                'folderList' => $this->folderList,
                'selectedFolder' => $this->selectedFolder,
                'levelOptions' => $this->levelOptions,
                'pageEntries' => [],
                'selectedIndex' => null,
                'tooLarge' => false,
            ]);
        }

        $tooLarge = $this->parser->exceedsSizeLimit($path);

        if ($tooLarge) {
            return view('livewire.admin.logs.index-component', [
                'logs' => $this->paginateEntries([]),
                'stats' => ['total' => 0],
                'fileInfo' => $this->parser->fileInfo($path),
                'fileList' => $this->fileList,
                'folderList' => $this->folderList,
                'selectedFolder' => $this->selectedFolder,
                'levelOptions' => $this->levelOptions,
                'pageEntries' => [],
                'selectedIndex' => null,
                'tooLarge' => true,
            ]);
        }

        $parsed = $this->parser->parse(
            $path,
            $this->search,
            $this->filterLevel,
            $this->dateFrom,
            $this->dateTo,
            $this->searchIncludeStack
        );

        // A5 — ascending order reverses the (already limited) display set.
        if ($this->sortDirection === 'asc') {
            $parsed = array_reverse($parsed);
        }

        $logs = $this->paginateEntries($parsed);

        // Keep the current page's entries so the detail panel can resolve rows.
        $this->pageEntries = array_values($logs->items());
        $this->syncSelectedEntry();

        // Stats computed on the *filtered* set so admins see real context.
        $stats = $this->parser->stats($parsed);

        return view('livewire.admin.logs.index-component', [
            'logs' => $logs,
            'stats' => $stats,
            'fileInfo' => $this->parser->fileInfo($path),
            'fileList' => $this->fileList,
            'folderList' => $this->folderList,
            'selectedFolder' => $this->selectedFolder,
            'levelOptions' => $this->levelOptions,
            'selectedIndex' => $this->selectedIndex,
            'tooLarge' => false,
        ]);
    }

    /**
     * Render the "diff entre fechas" view comparing two date ranges of the
     * same (possibly dropped/uploaded) log file.
     */
    private function renderDiff()
    {
        $path = $this->activeLogPath();
        $diff = $this->parser->diff(
            $path,
            $this->diffFromA ?: null,
            $this->diffToA ?: null,
            $this->diffFromB ?: null,
            $this->diffToB ?: null,
            $this->filterLevel ?: null,
            $this->search ?: null,
            $this->searchIncludeStack,
        );

        return view('livewire.admin.logs.index-component', [
            'logs' => new Collection,
            'stats' => ['total' => 0],
            'fileInfo' => $this->parser->fileInfo($path),
            'fileList' => $this->fileList,
            'folderList' => $this->folderList,
            'selectedFolder' => $this->selectedFolder,
            'levelOptions' => $this->levelOptions,
            'selectedIndex' => $this->selectedIndex,
            'tooLarge' => false,
            'diffResult' => $diff,
        ]);
    }

    /**
     * Render the viewer when a local .log file has been dropped/uploaded.
     */
    private function renderUploaded()
    {
        $path = $this->uploadedLog->getRealPath();

        if (! is_file($path) || $this->parser->exceedsSizeLimit($path)) {
            $this->notification()->error(
                title: 'Archivo inválido',
                description: 'El archivo subido no es un log o supera el tamaño permitido.'
            );
            $this->uploadedLog = null;
            $this->uploadedLogName = '';

            return view('livewire.admin.logs.index-component', [
                'logs' => $this->paginateEntries([]),
                'stats' => ['total' => 0],
                'fileInfo' => null,
                'fileList' => $this->fileList,
                'folderList' => $this->folderList,
                'selectedFolder' => $this->selectedFolder,
                'levelOptions' => $this->levelOptions,
                'pageEntries' => [],
                'selectedIndex' => null,
                'tooLarge' => false,
            ]);
        }

        $parsed = $this->parser->parse(
            $path,
            $this->search,
            $this->filterLevel,
            $this->dateFrom,
            $this->dateTo,
            $this->searchIncludeStack
        );

        if ($this->sortDirection === 'asc') {
            $parsed = array_reverse($parsed);
        }

        $logs = $this->paginateEntries($parsed);
        $this->pageEntries = array_values($logs->items());
        $this->syncSelectedEntry();

        return view('livewire.admin.logs.index-component', [
            'logs' => $logs,
            'stats' => $this->parser->stats($parsed),
            'fileInfo' => $this->parser->fileInfo($path),
            'fileList' => $this->fileList,
            'folderList' => $this->folderList,
            'selectedFolder' => $this->selectedFolder,
            'levelOptions' => $this->levelOptions,
            'selectedIndex' => $this->selectedIndex,
            'tooLarge' => false,
            'autoRefresh' => false,
        ]);
    }

    /**
     * Path of the file that is currently active (uploaded or on-disk selected).
     */
    private function activeLogPath(): string
    {
        if ($this->uploadedLog) {
            return $this->uploadedLog->getRealPath();
        }

        return $this->resolveSelectedPath();
    }

    // ─── Livewire trait handlers ───────────────────────────────
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterLevel()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingPaginate()
    {
        $this->resetPage();
    }

    public function updatingSelectedFile()
    {
        $this->resetPage();
        $this->filterLevel = '';
        $this->search = '';
        $this->searchIncludeStack = false;
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->selectedIndex = null;
        // B4 — picking an on-disk file leaves the dragged file behind.
        $this->uploadedLog = null;
        $this->uploadedLogName = '';
        $this->refreshFileList();
    }

    public function updatingSelectedFolder()
    {
        $this->resetPage();
        $this->filterLevel = '';
        $this->search = '';
        $this->searchIncludeStack = false;
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->selectedIndex = null;
        // Point at first available file in the new folder.
        $this->uploadedLog = null;
        $this->uploadedLogName = '';
        $this->refreshFileList();
        $this->selectedFile = array_key_first($this->fileList) ?? 'laravel.log';
    }

    // ─── Actions ───────────────────────────────────────────────
    public function selectFolder($name)
    {
        if (in_array($name, $this->folderList, true)) {
            $this->selectedFolder = $name;
            $this->resetPage();
            $this->refreshFileList();
            $this->selectedFile = array_key_first($this->fileList) ?? 'laravel.log';
        }
    }

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
        $this->selectedIndex = null;
        $this->resetPage();
    }

    /**
     * Toggle a level filter from a clickable level chip in the table.
     */
    public function filterByLevel($level)
    {
        $this->filterLevel = $this->filterLevel === $level ? '' : $level;
        $this->selectedIndex = null;
        $this->resetPage();
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = ! $this->autoRefresh;
        $this->resetPage();
    }

    /**
     * Flip the date sort order between newest-first and oldest-first.
     */
    public function toggleSort()
    {
        $this->sortDirection = $this->sortDirection === 'desc' ? 'asc' : 'desc';
        $this->selectedIndex = null;
        $this->resetPage();
    }

    /**
     * Shift the visible date range window by one day.
     *
     * $dir is +1 (move the window forward) or -1 (back a day). Both bounds are
     * shifted together when a range is set; otherwise the single bound moves.
     * The window is clamped so "from" never sits beyond today.
     */
    public function nudgeDateRange(int $dir)
    {
        $dir = $dir > 0 ? 1 : -1;

        if (! $this->dateFrom && ! $this->dateTo) {
            // Nothing selected yet: start from two days ago -> today.
            $this->dateFrom = now()->subDays(2)->format('Y-m-d');
            $this->dateTo = now()->format('Y-m-d');
            $this->resetPage();

            return;
        }

        $move = fn ($v) => \Carbon\Carbon::createFromFormat('Y-m-d', $v)?->addDays($dir)->format('Y-m-d');

        if ($this->dateFrom) {
            $this->dateFrom = $move($this->dateFrom);
        }
        if ($this->dateTo) {
            $this->dateTo = $move($this->dateTo);
        }

        // Clamp "from" not to exceed today.
        if ($this->dateFrom && $this->dateFrom > now()->format('Y-m-d')) {
            $this->dateFrom = now()->format('Y-m-d');
        }

        $this->selectedIndex = null;
        $this->resetPage();
    }

    /**
     * Select the entry shown in the side detail panel (by page index).
     */
    public function selectEntry($index)
    {
        if (! is_numeric($index) || ! array_key_exists((int) $index, $this->pageEntries)) {
            $this->selectedIndex = null;

            return;
        }

        $this->selectedIndex = (int) $index;
    }

    /**
     * Drop the selection when the selected index is no longer on screen.
     */
    private function syncSelectedEntry(): void
    {
        if ($this->selectedIndex === null) {
            return;
        }

        if (! array_key_exists($this->selectedIndex, $this->pageEntries)) {
            $this->selectedIndex = null;
        }
    }

    /**
     * Download the selected file (guarded, whitelisted against fileList).
     * For a dragged/uploaded file, download the local temp copy under its
     * original name.
     */
    public function download()
    {
        if ($this->uploadedLog) {
            $path = $this->uploadedLog->getRealPath();

            if (! is_file($path)) {
                $this->notification()->error('Error', 'El archivo temporal ya no existe.');

                return;
            }

            return Response::download($path, $this->uploadedLogName ?: $this->uploadedLog->getClientOriginalName());
        }

        if (! isset($this->fileList[$this->selectedFile])) {
            $this->notification()->error('Error', 'Archivo no válido para descargar.');

            return;
        }

        $path = $this->resolveSelectedPath();

        return Response::download($path, $this->selectedFile);
    }

    /**
     * Export the filtered log entries as a downloadable JSON payload.
     *
     * Honours the same size guard used for rendering, refuses files that are
     * too large, and applies the currently selected level/search/date filters.
     */
    public function exportJson()
    {
        // B4 — allow exporting the dragged/uploaded file too.
        if ($this->uploadedLog) {
            $path = $this->uploadedLog->getRealPath();
            $fileName = $this->uploadedLogName ?: $this->uploadedLog->getClientOriginalName();
        } elseif (isset($this->fileList[$this->selectedFile])) {
            $fileName = $this->selectedFile;
            $path = $this->resolveSelectedPath();
        } else {
            $this->notification()->error('Error', 'Archivo no válido para exportar.');

            return;
        }

        if (! is_file($path) || $this->parser->exceedsSizeLimit($path)) {
            $this->notification()->error('Error', 'El archivo es demasiado grande para exportar.');

            return;
        }

        $entries = $this->parser->parse(
            $path,
            $this->search,
            $this->filterLevel,
            $this->dateFrom,
            $this->dateTo,
            $this->searchIncludeStack
        );

        $payload = json_encode([
            'exported_at' => now()->toIso8601String(),
            'file' => $fileName,
            'filters' => [
                'level' => $this->filterLevel,
                'search' => $this->search,
                'date_from' => $this->dateFrom,
                'date_to' => $this->dateTo,
            ],
            'total' => count($entries),
            'entries' => $entries,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($payload === false) {
            $this->notification()->error('Error', 'No se pudo serializar el log a JSON.');

            return;
        }

        $stamp = now()->format('Ymd_His');
        $safeName = str_replace(['/', '\\'], '', pathinfo($fileName, PATHINFO_FILENAME));
        $tmp = tempnam(sys_get_temp_dir(), 'cfla_log_');
        file_put_contents($tmp, $payload);

        $filename = $safeName.'_'.$stamp.'.json';

        return Response::download($tmp, $filename, ['Content-Type' => 'application/json'])->deleteFileAfterSend(true);
    }

    // ─── B4 — drag & drop local ───────────────────────────────
    /**
     * Called by Livewire right after a file is uploaded via the drop zone.
     * Keeps track of the display name and drops any maintenance-only state.
     */
    public function updatedUploadedLog()
    {
        if (! $this->uploadedLog) {
            $this->uploadedLogName = '';

            return;
        }

        $this->uploadedLogName = $this->uploadedLog->getClientOriginalName();
        $this->filterLevel = '';
        $this->search = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->selectedIndex = null;
        $this->resetPage();
    }

    /**
     * Clear the dragged/uploaded file and return to the on-disk viewer.
     */
    public function removeUploadedLog()
    {
        $this->uploadedLog = null;
        $this->uploadedLogName = '';
        $this->resetPage();
    }

    // ─── B5 — diff entre fechas ───────────────────────────────
    /**
     * Enter diff mode, seeding the two ranges with sensible defaults.
     */
    public function enterDiff()
    {
        $this->diffMode = true;

        if (! $this->diffFromA && ! $this->diffToA) {
            $this->diffFromA = now()->subDays(4)->format('Y-m-d');
            $this->diffToA = now()->subDays(2)->format('Y-m-d');
        }
        if (! $this->diffFromB && ! $this->diffToB) {
            $this->diffFromB = now()->subDays(2)->format('Y-m-d');
            $this->diffToB = now()->format('Y-m-d');
        }
    }

    /**
     * Leave diff mode and return to the regular table.
     */
    public function exitDiff()
    {
        $this->diffMode = false;
    }

    /**
     * Swap the two compared ranges.
     */
    public function swapDiffRanges()
    {
        [$this->diffFromA, $this->diffFromB] = [$this->diffFromB, $this->diffFromA];
        [$this->diffToA, $this->diffToB] = [$this->diffToB, $this->diffToA];
    }

    public function confirmClean()
    {
        $this->confirmAction = 'clean';
    }

    public function confirmDelete()
    {
        $this->confirmAction = 'delete';
    }

    public function confirmDeleteAll()
    {
        $this->confirmAction = 'deleteAll';
    }

    public function confirmPrune()
    {
        $this->confirmAction = 'prune';
    }

    public function cancelAction()
    {
        $this->confirmAction = null;
    }

    /**
     * Empty (truncate) the selected log file, keeping the file itself.
     */
    public function cleanLog()
    {
        if ($this->confirmAction !== 'clean' || ! isset($this->fileList[$this->selectedFile])) {
            return;
        }

        $path = $this->resolveSelectedPath();
        File::put($path, '');

        $this->confirmAction = null;
        $this->flushParserCache();
        $this->notification()->success(
            'Log Limpiado',
            "El archivo {$this->selectedFile} se vació correctamente."
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

        $path = $this->resolveSelectedPath();

        try {
            // Safety net: keep a backup before destroying data.
            $backupDir = storage_path('logs/backups');
            File::ensureDirectoryExists($backupDir);
            $stamp = now()->format('Ymd_His');
            File::copy($path, $backupDir.'/'.$stamp.'_'.$this->selectedFile);

            File::delete($path);

            // Reset selection to an existing file.
            $this->refreshFileList();
            $this->selectedFile = array_key_first($this->fileList) ?? '';

            $this->confirmAction = null;
            $this->flushParserCache();
            $this->notification()->success(
                'Log Eliminado',
                "Se eliminó {$this->selectedFile}. Se guardó una copia en logs/backups/."
            );
        } catch (\Throwable $e) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se pudo eliminar el log: '.$e->getMessage()
            );
        }
    }

    /**
     * Delete every *.log file in the current folder (after backups).
     */
    public function deleteAllLogs()
    {
        if ($this->confirmAction !== 'deleteAll') {
            return;
        }

        try {
            // Back up each file before removing.
            $backupDir = storage_path('logs/backups');
            File::ensureDirectoryExists($backupDir);
            $stamp = now()->format('Ymd_His');

            foreach ($this->fileList as $name => $info) {
                $src = storage_path('logs/'.($this->selectedFolder ? $this->selectedFolder.'/' : '').$name);
                if (is_file($src)) {
                    File::copy($src, $backupDir.'/'.$stamp.'_'.$name);
                }
            }

            $deleted = $this->parser->deleteAllFiles($this->selectedFolder);

            $this->confirmAction = null;
            $this->refreshFileList();
            $this->selectedFile = array_key_first($this->fileList) ?? 'laravel.log';
            $this->flushParserCache();

            $this->notification()->success(
                'Logs Eliminados',
                "Se eliminaron {$deleted} archivo(s). Copias guardadas en logs/backups/."
            );
        } catch (\Throwable $e) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se pudieron eliminar los logs: '.$e->getMessage()
            );
        }
    }

    /**
     * Remove logs older than the configured retention window (mtime based).
     */
    public function pruneOldLogs()
    {
        if ($this->confirmAction !== 'prune') {
            return;
        }

        $days = max(1, (int) $this->pruneDays);

        try {
            $deleted = $this->parser->pruneFilesOlderThan($this->selectedFolder, $days);

            $this->confirmAction = null;
            $this->refreshFileList();
            $this->selectedFile = array_key_first($this->fileList) ?? 'laravel.log';
            $this->flushParserCache();

            $this->notification()->success(
                'Logs Antiguos Eliminados',
                "Se eliminaron {$deleted} archivo(s) con más de {$days} días."
            );
        } catch (\Throwable $e) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se pudieron eliminar los logs antiguos: '.$e->getMessage()
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
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => request()->query(),
        ]);
    }

    private function refreshFolderList()
    {
        $this->folderList = $this->parser->folders();

        if ($this->selectedFolder !== '' && ! in_array($this->selectedFolder, $this->folderList, true)) {
            $this->selectedFolder = '';
        }
    }

    private function refreshFileList()
    {
        $files = $this->parser->files($this->selectedFolder);
        $map = [];

        foreach ($files as $file) {
            $map[$file['name']] = $file;
        }

        $this->fileList = $map;
    }

    /**
     * Resolve the absolute path for the currently selected file, honoring
     * the chosen sub-folder. Never goes outside the logs base directory.
     */
    private function resolveSelectedPath(): string
    {
        return $this->parser->resolvePath($this->selectedFolder, $this->selectedFile);
    }

    private function flushParserCache()
    {
        // Invalidate any cached content/parse of the affected file.
        // The parse key appends filemtime, but we drop both caches defensively.
        $path = $this->resolveSelectedPath();
        $digest = md5($path);

        if (function_exists('cache')) {
            cache()->forget('log-viewer.content.'.$digest);
            cache()->forget('log-viewer.files.'.md5($this->selectedFolder));
            // Parse keys are time-stamped; remove the currently known one.
            $stamp = (int) @filemtime($path);
            cache()->forget('log-viewer.parse.'.$digest.'.'.$stamp);
        }
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
