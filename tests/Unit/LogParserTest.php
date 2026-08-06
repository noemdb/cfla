<?php

namespace Tests\Unit;

use App\Livewire\Admin\Logs\Services\LogParser;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class LogParserTest extends TestCase
{
    private LogParser $parser;

    private string $base;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new LogParser;
        $this->base = storage_path('logs');
    }

    protected function tearDown(): void
    {
        // Clean up sub-folders created for testing (never touch real root logs).
        foreach (['_test_a', '_test_b'] as $d) {
            File::deleteDirectory(storage_path('logs/'.$d), true);
        }
        foreach (['_test_stack.log', '_test_diff.log', '_test_diff2.log', '_test_diff3.log'] as $f) {
            @unlink($this->base.'/'.$f);
        }
        parent::tearDown();
    }

    private function seedFolderFiles(): void
    {
        File::ensureDirectoryExists($this->base.'/_test_a');
        File::ensureDirectoryExists($this->base.'/_test_b');
        File::put($this->base.'/_test_a/app.log', '[2024-01-01 10:00:00] local.ERROR: First');
        File::put($this->base.'/_test_b/worker.log', '[2024-01-01 10:00:00] local.INFO: Second');
    }

    public function test_folders_excludes_backups(): void
    {
        File::ensureDirectoryExists($this->base.'/_test_a');
        File::ensureDirectoryExists($this->base.'/backups');

        $folders = $this->parser->folders();

        $this->assertContains('_test_a', $folders);
        $this->assertNotContains('backups', $folders);
    }

    public function test_files_filters_by_subfolder(): void
    {
        $this->seedFolderFiles();

        $a = $this->parser->files('_test_a');
        $b = $this->parser->files('_test_b');

        $this->assertCount(1, $a);
        $this->assertCount(1, $b);
        $this->assertEquals('app.log', $a[0]['name']);
        $this->assertEquals('worker.log', $b[0]['name']);
    }

    public function test_resolve_path_rejects_traversal(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->parser->resolvePath('', '../../secret.log');
    }

    public function test_resolve_path_returns_safe_absolute_path(): void
    {
        $this->seedFolderFiles();
        $path = $this->parser->resolvePath('_test_a', 'app.log');
        $this->assertEquals($this->base.'/_test_a/app.log', $path);
        $this->assertTrue(is_file($path));
    }

    public function test_delete_all_files_clears_folder_only(): void
    {
        $this->seedFolderFiles();
        $deleted = $this->parser->deleteAllFiles('_test_a');

        $this->assertEquals(1, $deleted);
        $this->assertFalse(is_file($this->base.'/_test_a/app.log'));
        // Untouched folder keeps its file.
        $this->assertTrue(is_file($this->base.'/_test_b/worker.log'));
    }

    public function test_prune_older_than_deletes_only_old_files(): void
    {
        File::ensureDirectoryExists($this->base.'/_test_a');
        $old = $this->base.'/_test_a/old.log';
        $recent = $this->base.'/_test_a/recent.log';
        File::put($old, 'x');
        File::put($recent, 'x');
        touch($old, time() - (40 * 86400)); // 40 days old
        touch($recent, time() - (2 * 86400)); // 2 days old

        $deleted = $this->parser->pruneFilesOlderThan('_test_a', 30);

        $this->assertEquals(1, $deleted);
        $this->assertFalse(is_file($old));
        $this->assertTrue(is_file($recent));
    }

    public function test_search_stack_matches_trace_only_when_enabled(): void
    {
        $path = $this->base.'/_test_stack.log';
        $raw = "[2024-01-01 10:00:00] local.ERROR: Fatal boom\nStack trace:\n#0 Vendor\\Package\\Service.php(42): App\\OrderController->run\n#1 app/Http/Controllers/HomeController.php:15";
        file_put_contents($path, $raw);

        // Term only present in the stack: not found by default.
        $this->assertSame([], $this->parser->parse($path, 'HomeController'));

        // Found when the searchStack flag is enabled.
        $matches = $this->parser->parse($path, 'HomeController', null, null, null, true);
        $this->assertCount(1, $matches);
        $this->assertSame('ERROR', $matches[0]['level']);
    }

    public function test_diff_detects_added_removed_and_common_entries(): void
    {
        $path = $this->base.'/_test_diff.log';
        $raw = '';
        // Range A: two lines.
        $raw .= "[2024-01-01 10:00:00] local.INFO: A only line\n";
        $raw .= "[2024-01-01 10:05:00] local.ERROR: common error\n";
        // Range B: one line repeated from A + a new one.
        $raw .= "[2024-01-02 10:00:00] local.ERROR: common error\n";
        $raw .= "[2024-01-02 10:30:00] local.INFO: B only line\n";
        file_put_contents($path, $raw);

        $result = $this->parser->diff(
            $path,
            '2024-01-01',
            '2024-01-01',
            '2024-01-02',
            '2024-01-02'
        );

        // A has 2 entries, B has 2 entries.
        $this->assertSame(2, $result['rangeA']['total']);
        $this->assertSame(2, $result['rangeB']['total']);

        // "common error" appears in both ranges -> 1 common distinct line.
        $this->assertSame(1, $result['common']);

        // Only in A: "A only line". Only in B: "B only line".
        $this->assertCount(1, $result['removed']);
        $this->assertCount(1, $result['added']);
        $this->assertStringContainsString('A only line', $result['removed'][0]['message']);
        $this->assertStringContainsString('B only line', $result['added'][0]['message']);
    }

    public function test_diff_counts_repeated_lines_as_common(): void
    {
        $path = $this->base.'/_test_diff2.log';
        // The same line repeated many times in both ranges collapses to one
        // distinct line -> common is NOT the number of raw rows.
        $raw = '';
        for ($i = 0; $i < 3; $i++) {
            $raw .= "[2024-01-01 10:0{$i}:00] local.WARNING: spam\n";
        }
        for ($i = 0; $i < 4; $i++) {
            $raw .= "[2024-01-02 10:0{$i}:00] local.WARNING: spam\n";
        }
        file_put_contents($path, $raw);

        $result = $this->parser->diff($path, '2024-01-01', '2024-01-01', '2024-01-02', '2024-01-02');

        // Same content line in both ranges -> 1 common, no differences.
        $this->assertSame(1, $result['common']);
        $this->assertCount(0, $result['added']);
        $this->assertCount(0, $result['removed']);
    }

    public function test_diff_respects_level_and_search_filters(): void
    {
        $path = $this->base.'/_test_diff3.log';
        $raw = "[2024-01-01 10:00:00] local.INFO: keep me\n"
            ."[2024-01-01 10:05:00] local.ERROR: keep me\n"
            ."[2024-01-02 10:00:00] local.ERROR: keep me\n"
            ."[2024-01-02 10:30:00] local.ERROR: other thing\n";
        file_put_contents($path, $raw);

        $result = $this->parser->diff(
            $path,
            '2024-01-01', '2024-01-01',
            '2024-01-02', '2024-01-02',
            'ERROR'
        );

        // INFO lines are filtered out; the shared ERROR line is common.
        $this->assertSame(1, $result['common']);
        $this->assertCount(0, $result['removed']); // ERROR "keep me" also in B
        $this->assertCount(1, $result['added']);   // "other thing" only in B
    }
}
