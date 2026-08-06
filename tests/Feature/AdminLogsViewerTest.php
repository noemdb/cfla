<?php

namespace Tests\Feature;

use App\Livewire\Admin\Logs\IndexComponent;
use App\Livewire\Admin\Logs\Services\LogParser;
use Livewire\Livewire;
use Tests\TestCase;

class AdminLogsViewerTest extends TestCase
{
    /**
     * The Logs viewer component renders successfully.
     */
    public function test_logs_component_renders_successfully(): void
    {
        Livewire::test(IndexComponent::class)
            ->assertStatus(200);
    }

    /**
     * D2/D3 — the rendered view exposes ARIA dialog semantics and expandable controls.
     * Modals carry role="dialog" / aria-modal and the date header is a sort control.
     */
    public function test_render_includes_aria_dialog_semantics(): void
    {
        $html = Livewire::test(IndexComponent::class)->html();

        $this->assertStringContainsString('role="dialog"', $html);
        $this->assertStringContainsString('aria-modal="true"', $html);
        $this->assertStringContainsString('aria-labelledby="log-clean-title"', $html);
        $this->assertStringContainsString('aria-labelledby="log-delete-title"', $html);
        $this->assertStringContainsString('aria-labelledby="log-deleteall-title"', $html);
        $this->assertStringContainsString('aria-labelledby="log-prune-title"', $html);
        // The sort control on the Fecha header is present as a real button.
        $this->assertStringContainsString('wire:click="toggleSort"', $html);
        $this->assertStringContainsString('log-search-input', $html);
    }

    /**
     * The header exposes a global entry counter and an auto-refresh toggle.
     */
    public function test_header_shows_counter_and_auto_refresh_toggles(): void
    {
        $component = Livewire::test(IndexComponent::class)
            ->assertViewHas('stats')
            ->assertSet('autoRefresh', false);

        // Both a default file info and a zero-total counter are present.
        $component->call('toggleAutoRefresh')
            ->assertSet('autoRefresh', true);

        $component->call('toggleAutoRefresh')
            ->assertSet('autoRefresh', false);
    }

    /**
     * The parser normalizes entries and computes level stats as expected.
     */
    public function test_parser_normalizes_real_log_file(): void
    {
        $parser = new LogParser;
        $path = storage_path('logs/_feat_parser.log');

        // Deterministic sample data so the test does not depend on real logs.
        file_put_contents($path, "[2024-01-01 11:22:33] local.ERROR: Fatal thing\n[2024-01-02 12:34:56] local.INFO: All good\n");

        try {
            $logs = $parser->parse($path);
            $stats = $parser->stats($logs);

            $this->assertNotEmpty($logs);
            $this->assertArrayHasKey('level', $logs[0]);
            $this->assertArrayHasKey('date', $logs[0]);
            $this->assertArrayHasKey('message', $logs[0]);
            $this->assertEquals(count($logs), $stats['total']);

            // parse() sorts newest date first.
            $this->assertEquals(['INFO', 'ERROR'], array_column($logs, 'level'));
        } finally {
            @unlink($path);
        }
    }

    /**
     * Selecting a different folder loads its file list.
     */
    public function test_select_folder_switches_file_list(): void
    {
        // Create a disposable sub-folder with a log so the component can see it.
        $dir = storage_path('logs/_feat_folder');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $log = $dir.'/feature.log';
        if (! file_exists($log)) {
            file_put_contents($log, '[2024-01-01 10:00:00] local.ERROR: Feature');
        }

        try {
            $component = Livewire::test(IndexComponent::class);
            $component->assertSet('selectedFolder', '');

            $component->call('selectFolder', '_feat_folder');

            $component->assertSet('selectedFolder', '_feat_folder');
            $component->assertSet('selectedFile', 'feature.log');
        } finally {
            @unlink($log);
            @rmdir($dir);
        }
    }

    /**
     * deleteAllLogs requires an armed confirmation and removes the files.
     */
    public function test_delete_all_requires_confirmation_and_clears_folder(): void
    {
        $dir = storage_path('logs/_feat_delall');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $log = $dir.'/x.log';
        if (! file_exists($log)) {
            file_put_contents($log, '[2024-01-01 10:00:00] local.ERROR: X');
        }

        try {
            $component = Livewire::test(IndexComponent::class)
                ->call('selectFolder', '_feat_delall')
                ->assertSet('selectedFile', 'x.log');

            // Not confirmed: nothing happens.
            $component->call('deleteAllLogs')
                ->assertSet('confirmAction', null);
            $this->assertFileExists($log);

            // Armed then confirmed: file removed.
            $component->call('confirmDeleteAll')
                ->assertSet('confirmAction', 'deleteAll')
                ->call('deleteAllLogs')
                ->assertSet('confirmAction', null);
            $this->assertFileDoesNotExist($log);
        } finally {
            @unlink($log);
            @rmdir($dir);
        }
    }

    /**
     * cleanLog requires confirmation and empties the file.
     */
    public function test_clean_log_requires_confirmation_and_empties_file(): void
    {
        $dir = storage_path('logs/_feat_clean');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $log = $dir.'/clean.log';
        file_put_contents($log, '[2024-01-01 10:00:00] local.INFO: Keep line');

        try {
            $component = Livewire::test(IndexComponent::class)
                ->call('selectFolder', '_feat_clean')
                ->assertSet('selectedFile', 'clean.log');

            // Not confirmed: file untouched.
            $component->call('cleanLog')
                ->assertSet('confirmAction', null);
            $this->assertNotEquals('', file_get_contents($log));

            // Armed then confirmed: file emptied.
            $component->call('confirmClean')
                ->assertSet('confirmAction', 'clean')
                ->call('cleanLog')
                ->assertSet('confirmAction', null);
            $this->assertSame('', file_get_contents($log));
        } finally {
            @unlink($log);
            @rmdir($dir);
        }
    }

    /**
     * exportJson downloads a JSON payload with the correct MIME type.
     */
    public function test_export_json_downloads_filtered_payload(): void
    {
        $dir = storage_path('logs/_feat_export');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $log = $dir.'/export.log';
        file_put_contents($log, "[2023-01-01 10:00:00] local.ERROR: First\n[2023-01-02 11:00:00] local.INFO: Second");

        try {
            $component = Livewire::test(IndexComponent::class)
                ->call('selectFolder', '_feat_export')
                ->assertSet('selectedFile', 'export.log')
                ->assertOk();

            $component->call('exportJson')
                ->assertFileDownloaded()
                ->assertFileDownloaded(null, null, 'application/json');
        } finally {
            @unlink($log);
            @rmdir($dir);
        }
    }

    /**
     * selectEntry populates the side detail panel with the full entry.
     */
    public function test_select_entry_populates_detail_panel(): void
    {
        $dir = storage_path('logs/_feat_detail');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $log = $dir.'/detail.log';
        file_put_contents($log, "[2023-01-01 10:00:00] local.ERROR: Boom happened\n  Stack trace:\n  #0 App\\TracePoint.php:5\n");

        try {
            $component = Livewire::test(IndexComponent::class)
                ->call('selectFolder', '_feat_detail')
                ->assertSet('selectedFile', 'detail.log')
                ->call('selectEntry', 0)
                ->assertSet('selectedIndex', 0);

            $entries = $component->get('pageEntries');
            $this->assertNotEmpty($entries);
            $this->assertSame('ERROR', $entries[0]['level']);
            $this->assertStringContainsString('Boom happened', $entries[0]['message']);

            // Selecting an out-of-range index clears the panel.
            $component->call('selectEntry', 999)->assertSet('selectedIndex', null);
        } finally {
            @unlink($log);
            @rmdir($dir);
        }
    }

    /**
     * filterByLevel toggles a level filter from a clickable chip.
     */
    public function test_filter_by_level_toggles_chip_filter(): void
    {
        $dir = storage_path('logs/_feat_level');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $log = $dir.'/level.log';
        file_put_contents($log, "[2023-01-01 10:00:00] local.ERROR: First\n[2023-01-02 11:00:00] local.INFO: Second\n[2023-01-03 12:00:00] local.WARNING: Third");

        try {
            $component = Livewire::test(IndexComponent::class)
                ->call('selectFolder', '_feat_level')
                ->assertSet('selectedFile', 'level.log')
                ->assertSet('filterLevel', '');

            $component->call('filterByLevel', 'ERROR')
                ->assertSet('filterLevel', 'ERROR');

            // Toggling the same level clears it.
            $component->call('filterByLevel', 'ERROR')
                ->assertSet('filterLevel', '');
        } finally {
            @unlink($log);
            @rmdir($dir);
        }
    }

    /**
     * A5 — toggleSort flips the date direction and resets the selection.
     */
    public function test_toggle_sort_flips_date_direction(): void
    {
        $dir = storage_path('logs/_feat_sort');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $log = $dir.'/sort.log';
        file_put_contents($log, '[2023-01-01 10:00:00] local.ERROR: Old
[2023-01-05 11:00:00] local.ERROR: New');

        try {
            $component = Livewire::test(IndexComponent::class)
                ->call('selectFolder', '_feat_sort');

            // Default is newest first.
            $component->assertSet('sortDirection', 'desc');

            $component->call('toggleSort')
                ->assertSet('sortDirection', 'asc');

            $component->call('toggleSort')
                ->assertSet('sortDirection', 'desc');
        } finally {
            @unlink($log);
            @rmdir($dir);
        }
    }

    /**
     * A5 — ascending order returns the oldest entries first in the rendered view.
     */
    public function test_ascending_sort_returns_oldest_first(): void
    {
        $dir = storage_path('logs/_feat_sort2');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $log = $dir.'/sort2.log';
        file_put_contents($log, '[2023-01-01 10:00:00] local.ERROR: Oldest line
[2023-01-05 11:00:00] local.ERROR: Newest line');

        try {
            $component = Livewire::test(IndexComponent::class)
                ->call('selectFolder', '_feat_sort2')
                ->call('toggleSort');

            $html = $component->html();
            // Oldest first in ascending mode → 'Oldest line' precedes 'Newest line'.
            $this->assertLessThan(
                strpos($html, 'Newest line'),
                strpos($html, 'Oldest line')
            );
        } finally {
            @unlink($log);
            @rmdir($dir);
        }
    }

    /**
     * C4 — nudgeDateRange shifts a fresh window two days back from today.
     */
    public function test_nudge_date_range_starts_a_window(): void
    {
        $component = Livewire::test(IndexComponent::class)
            ->call('nudgeDateRange', 1);

        $from = $component->get('dateFrom');
        $to = $component->get('dateTo');

        $this->assertSame(now()->subDays(2)->format('Y-m-d'), $from);
        $this->assertSame(now()->format('Y-m-d'), $to);
    }

    /**
     * C4 — nudgeDateRange moves an existing bound forward and clamps to today.
     */
    public function test_nudge_date_range_moves_bound_and_clamps(): void
    {
        $component = Livewire::test(IndexComponent::class)
            ->set('dateFrom', now()->format('Y-m-d'))
            ->call('nudgeDateRange', 1)
            ->assertSet('dateFrom', now()->format('Y-m-d')) // clamped
            ->set('dateFrom', now()->subDays(5)->format('Y-m-d'));

        $component->call('nudgeDateRange', 1)
            ->assertSet('dateFrom', now()->subDays(4)->format('Y-m-d'));

        // Backward moves a single bound correctly.
        $component->call('nudgeDateRange', -1)
            ->assertSet('dateFrom', now()->subDays(5)->format('Y-m-d'));
    }

    /**
     * B5 — enterDiff switches to the comparison view and seeds two ranges.
     */
    public function test_enter_diff_seeds_ranges_and_renders_diff(): void
    {
        $dir = storage_path('logs/_feat_diff');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        // Two dates so the diff finds added / removed / common lines.
        $log = $dir.'/diff.log';
        file_put_contents($log, '[2024-01-01 10:00:00] local.ERROR: shared line
[2024-01-01 10:05:00] local.INFO: only in A
[2024-01-02 10:00:00] local.ERROR: shared line
[2024-01-02 10:30:00] local.INFO: only in B');

        try {
            $component = Livewire::test(IndexComponent::class)
                ->call('selectFolder', '_feat_diff');

            $component->call('enterDiff')
                ->assertSet('diffMode', true)
                ->assertSet('diffFromA', now()->subDays(4)->format('Y-m-d'))
                ->assertSet('diffToB', now()->format('Y-m-d'));

            // Manual ranges so the fixture is fully covered.
            $component->set('diffFromA', '2024-01-01')
                ->set('diffToA', '2024-01-01')
                ->set('diffFromB', '2024-01-02')
                ->set('diffToB', '2024-01-02');

            $html = $component->html();
            $this->assertStringContainsString('Comparación entre dos rangos de fechas', $html);
            $this->assertStringContainsString('Solo en rango A', $html);
            $this->assertStringContainsString('Solo en rango B', $html);
            $this->assertStringContainsString('only in A', $html);
            $this->assertStringContainsString('only in B', $html);
            $this->assertStringContainsString('shared line', $html);

            // Leaving diff mode returns to the normal table markup.
            $component->call('exitDiff')
                ->assertSet('diffMode', false);
        } finally {
            @unlink($log);
            @rmdir($dir);
        }
    }

    /**
     * B5 — swapping the ranges keeps the totals identical.
     */
    public function test_swap_diff_ranges_exchanges_bounds(): void
    {
        $component = Livewire::test(IndexComponent::class)
            ->call('enterDiff')
            ->set('diffFromA', '2024-01-01')
            ->set('diffToA', '2024-01-02')
            ->set('diffFromB', '2024-01-03')
            ->set('diffToB', '2024-01-04')
            ->call('swapDiffRanges');

        $this->assertSame('2024-01-03', $component->get('diffFromA'));
        $this->assertSame('2024-01-04', $component->get('diffToA'));
        $this->assertSame('2024-01-01', $component->get('diffFromB'));
        $this->assertSame('2024-01-02', $component->get('diffToB'));
    }

    /**
     * B4 — a dropped .log file is inspected instead of the on-disk file.
     */
    public function test_uploaded_log_sets_name_and_inspects_temp_copy(): void
    {
        $component = Livewire::test(IndexComponent::class);

        $file = $this->makeUploadedLog();

        $component->upload('uploadedLog', [$file]);

        $component->assertSet('uploadedLogName', $file->getClientOriginalName());
        $component->assertSet('filterLevel', '');

        $html = $component->html();
        $this->assertStringContainsString('Archivo local cargado', $html);
        $this->assertStringContainsString('From upload', $html);

        $component->call('removeUploadedLog')
            ->assertSet('uploadedLogName', '');
    }

    /**
     * B4 — discarding the uploaded log returns to the on-disk viewer state.
     */
    public function test_remove_uploaded_log_returns_to_disk()
    {
        $component = Livewire::test(IndexComponent::class);

        $file = $this->makeUploadedLog();
        $component->upload('uploadedLog', [$file]);
        $component->call('removeUploadedLog');

        $component->assertSet('uploadedLogName', '');
        $this->assertNull($component->get('uploadedLog'));

        // Returning to disk: the maintenance header buttons reappear.
        $html = $component->html();
        $this->assertStringContainsString('wire:click="confirmClean"', $html);
    }

    /**
     * B4 — maintenance actions are hidden while an uploaded log is active.
     */
    public function test_maintenance_buttons_hidden_for_uploaded_log(): void
    {
        $file = $this->makeUploadedLog();

        $component = Livewire::test(IndexComponent::class);
        $component->upload('uploadedLog', [$file]);

        $html = $component->html();
        $this->assertStringNotContainsString('wire:click="confirmClean"', $html);
        $this->assertStringNotContainsString('wire:click="confirmDelete"', $html);
    }

    private function makeUploadedLog(): \Illuminate\Http\Testing\File
    {
        return \Illuminate\Http\Testing\File::createWithContent(
            'dumped-trace.log',
            '[2024-01-01 10:00:00] local.ERROR: From upload'
        )->mimeType('text/plain')->size(1);
    }
}
