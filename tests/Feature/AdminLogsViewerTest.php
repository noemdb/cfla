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
     * The parser normalizes entries and computes level stats as expected.
     */
    public function test_parser_normalizes_real_log_file(): void
    {
        $parser = new LogParser();
        $path = storage_path('logs/laravel.log');

        if (! file_exists($path)) {
            $this->markTestSkipped('No log file available to test against.');
        }

        $logs = $parser->parse($path);
        $stats = $parser->stats($logs);

        $this->assertNotEmpty($logs);
        $this->assertArrayHasKey('level', $logs[0]);
        $this->assertArrayHasKey('date', $logs[0]);
        $this->assertArrayHasKey('message', $logs[0]);
        $this->assertEquals(count($logs), $stats['total']);
    }
}
