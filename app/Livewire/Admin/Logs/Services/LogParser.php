<?php

namespace App\Livewire\Admin\Logs\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

/**
 * Pure, framework-agnostic parser for Monolog-style Laravel log files.
 *
 * This service has NO dependency on Livewire, Request or the session so it can
 * be unit-tested in isolation. It reads storage/logs/*.log and normalizes each
 * entry into a structured array:
 *
 *   [
 *       'level'   => 'ERROR'|'INFO'|...,
 *       'date'    => 'YYYY-MM-DD HH:MM:SS',
 *       'env'     => 'local'|'production',
 *       'message' => 'The log message',
 *       'context' => '{"exception": ...}',  // raw JSON string
 *       'stack'   => 'Full stack trace text',
 *       'hash'    => 'stable sha1 for expand/collapse toggles in the UI',
 *   ]
 */
class LogParser
{
    /** Above this file size (bytes) we refuse to render for safety/performance. */
    public const SIZE_LIMIT = 50 * 1024 * 1024;

    /** Regex used to detect the start of a new log entry. */
    private const ENTRY_START = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(DEBUG|INFO|NOTICE|WARNING|ERROR|CRITICAL|ALERT|EMERGENCY): (.*)$/';

    /** Hard cap on parsed entries per file to keep render() fast as logs grow. */
    public const MAX_ENTRIES = 2000;

    /** Size of the date window (bytes) used for the date filter pre-pass. */
    private const DATE_FILTER_WINDOW = 512000;

    /**
     * List every *.log file available in the logs directory.
     *
     * @return array<int, array{name: string, path: string, size: int, modified: int}>
     */
    public function files(): array
    {
        return Cache::remember('log-viewer.files', 30, function () {
            $path = storage_path('logs');
            $result = [];

            foreach (glob($path . '/*.log') ?: [] as $file) {
                $result[] = [
                    'name'     => basename($file),
                    'path'     => $file,
                    'size'     => (int) @filesize($file),
                    'modified' => (int) @filemtime($file),
                ];
            }

            // Deterministic order, newest modified first.
            usort($result, fn ($a, $b) => $b['modified'] <=> $a['modified']);

            return $result;
        });
    }

    /**
     * Whether a file is too large to render in the browser.
     */
    public function exceedsSizeLimit(string $path): bool
    {
        return @filesize($path) > self::SIZE_LIMIT;
    }

    /**
     * Parse a log file into normalized entries.
     *
     * @return array<int, array<string, mixed>>
     */
    public function parse(string $path, ?string $search = null, ?string $level = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        if (! is_file($path) || $this->exceedsSizeLimit($path)) {
            return [];
        }

        $cacheKey = 'log-viewer.parse.' . md5($path) . '.' . (int) @filemtime($path);
        $raw = Cache::remember($cacheKey, 60, function () use ($path) {
            return @file_get_contents($path) ?: '';
        });

        $entries = array_map(fn ($chunk) => $this->normalize((string) $chunk), $this->split($raw));

        // Apply filters in memory (files are small thanks to the size guard above).
        if ($level) {
            $entries = array_values(array_filter($entries, fn ($e) => $e['level'] === strtoupper($level)));
        }
        if ($search) {
            $needle = mb_strtolower($search);
            $entries = array_values(array_filter($entries, fn ($e) => str_contains(mb_strtolower($e['message']), $needle)));
        }
        if ($dateFrom) {
            $entries = array_values(array_filter($entries, fn ($e) => $e['date'] >= $dateFrom));
        }
        if ($dateTo) {
            $entries = array_values(array_filter($entries, fn ($e) => $e['date'] <= $dateTo));
        }

        // Keep newest last (chronological read order) but cap the amount we hold.
        $entries = array_slice($entries, -self::MAX_ENTRIES);

        // Sort by date descending by default (most recent first).
        usort($entries, fn ($a, $b) => strcmp($b['date'], $a['date']));

        return $entries;
    }

    /**
     * Compute a count summary grouped by level.
     *
     * @param array<int, array<string, mixed>> $logs
     * @return array<string, int>
     */
    public function stats(array $logs): array
    {
        $order = ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'];

        $counts = array_fill_keys($order, 0);
        foreach ($logs as $log) {
            $counts[$log['level']] = ($counts[$log['level']] ?? 0) + 1;
        }

        // Append anything unknown (keeps determinism) then compact nulls.
        foreach ($counts as $k => $v) {
            if ($v === 0) {
                unset($counts[$k]);
            }
        }

        // Re-add fallback so views can always render a full row.
        $counts['total'] = count($logs);

        return $counts;
    }

    /**
     * Build a path + size summary for the currently selected file.
     *
     * @return array{name: string, path: string, size: int, modified: int}
     */
    public function fileInfo(string $path): array
    {
        return [
            'name'     => basename($path),
            'path'     => $path,
            'size'     => (int) @filesize($path),
            'modified' => (int) @filemtime($path),
        ];
    }

    /**
     * Split the raw file content into entry "chunks" (message line + trailing data).
     *
     * @return array<int, string>
     */
    private function split(string $content): array
    {
        // Defensive: force UTF-8 and strip BOM.
        $content = preg_replace('/^\xEF\xBB\xBF/', '', (string) $content);
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

        $chunks = [];
        $lines  = preg_split('/\r\n|\r|\n/', $content);

        foreach ($lines as $line) {
            if ($line === '') {
                // Blank line always ends the current chunk's trailing stack block.
                if (! empty($chunks)) {
                    $chunks[count($chunks) - 1] .= "\n";
                }
                continue;
            }

            if (preg_match(self::ENTRY_START, $line)) {
                // New entry begins.
                $chunks[] = $line;
                continue;
            }

            // Continuation of the previous chunk (stack trace / context).
            if (! empty($chunks)) {
                $chunks[count($chunks) - 1] .= "\n" . $line;
            }
        }

        return $chunks;
    }

    /**
     * Normalize a chunk string into a structured entry array.
     */
    private function normalize(string $chunk): array
    {
        $firstNL = strpos($chunk, "\n");
        $firstLine = $firstNL === false ? $chunk : substr($chunk, 0, $firstNL);
        $rest = $firstNL === false ? '' : substr($chunk, $firstNL + 1);

        if (! preg_match(self::ENTRY_START, $firstLine, $m)) {
            // Unparseable — keep as a fallback entry so we never silently lose data.
            return [
                'level'   => 'INFO',
                'date'    => '',
                'env'     => '',
                'message' => trim($chunk),
                'context' => '',
                'stack'   => '',
                'hash'    => hash('sha1', $chunk),
            ];
        }

        $date    = $m[1];
        $env     = $m[2];
        $level   = $m[3];
        $message = trim($m[4]);

        // Split inline JSON context off the message if present.
        $context = '';
        if (preg_match('/\s+(\{.*\})\s*$/', $message, $cm)) {
            $context = $cm[1];
            $message = trim(substr($message, 0, - (strlen($cm[1]) + 1))); // also drop leading space
            $message = rtrim($message, ' ');
        }

        return [
            'level'   => $level,
            'date'    => $date,
            'env'     => $env,
            'message' => $message,
            'context' => $context,
            'stack'   => trim($rest),
            'hash'    => hash('sha1', $chunk),
        ];
    }
}
