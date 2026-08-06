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

    /** Default folder when none is selected. */
    public const ROOT_FOLDER = '';

    /** Sub-folder used internally for automatic backups. */
    public const BACKUP_FOLDER = 'backups';

    /**
     * Base directory where log files live (and sub-folders are scanned).
     */
    private function basePath(): string
    {
        return storage_path('logs');
    }

    /**
     * Resolve a safe, absolute path for the given folder/file combination.
     *
     * Guards against path traversal (../) by resolving symlinks and verifying
     * the result is inside the configured base directory.
     *
     * @param  string  $folder  sub-folder name relative to basePath(), '' = root
     * @param  string|null  $name  file name within the folder
     */
    public function resolvePath(string $folder = self::ROOT_FOLDER, ?string $name = null): string
    {
        $base = realpath($this->basePath()) ?: $this->basePath();

        // Normalize folder: strip leading/trailing slashes and dangerous '..' segments.
        $folder = trim(str_replace('..', '', $folder), '/\\');

        // Canonicalize the directory itself (folder must exist for a file view).
        $dir = realpath($folder === '' ? $base : $base.DIRECTORY_SEPARATOR.$folder)
            ?: ($folder === '' ? $base : $base.DIRECTORY_SEPARATOR.$folder);

        // Strip separator chars and '..' from the file name so it can never
        // escape the (already canonicalized) directory. Reject traversal.
        if ($name !== null && (str_contains($name, '..') || str_starts_with($name, '/') || str_starts_with($name, '\\'))) {
            throw new \InvalidArgumentException('Ruta de log no válida.');
        }

        $cleanName = $name !== null ? str_replace(['/', '\\'], '', $name) : null;

        $candidate = $cleanName !== null
            ? $dir.DIRECTORY_SEPARATOR.$cleanName
            : $dir;

        $normalized = str_replace('\\', '/', $candidate);
        $baseNorm = str_replace('\\', '/', $base);

        // Never allow escaping the logs base directory.
        if ($normalized !== $baseNorm && ! str_starts_with($normalized, $baseNorm.'/')) {
            throw new \InvalidArgumentException('Ruta de log no válida.');
        }

        return str_replace('/', DIRECTORY_SEPARATOR, $normalized);
    }

    /**
     * List top-level sub-folders inside the logs directory (excluding backups).
     *
     * @return array<int, string> folder names, sorted alphabetically
     */
    public function folders(): array
    {
        $base = $this->resolvePath(self::ROOT_FOLDER);
        $folders = [];

        foreach (glob($base.'/*') ?: [] as $node) {
            if (is_dir($node) && basename($node) !== self::BACKUP_FOLDER) {
                $folders[] = basename($node);
            }
        }

        sort($folders);

        return $folders;
    }

    /**
     * List every *.log file available in a given sub-folder (or root).
     *
     * @param  string  $folder  sub-folder name; self::ROOT_FOLDER for base
     * @return array<int, array{name: string, path: string, size: int, modified: int}>
     */
    public function files(string $folder = self::ROOT_FOLDER): array
    {
        return Cache::remember('log-viewer.files.'.md5($folder), 30, function () use ($folder) {
            $dir = $this->resolvePath($folder, null);
            $result = [];

            foreach (glob($dir.'/*.log') ?: [] as $file) {
                $result[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'size' => (int) @filesize($file),
                    'modified' => (int) @filemtime($file),
                ];
            }

            // Deterministic order, newest modified first.
            usort($result, fn ($a, $b) => $b['modified'] <=> $a['modified']);

            return $result;
        });
    }

    /**
     * Delete every *.log file in the given folder (preserving backups).
     *
     * @param  string  $folder  sub-folder name
     * @return int number of files deleted
     */
    public function deleteAllFiles(string $folder = self::ROOT_FOLDER): int
    {
        $dir = $this->resolvePath($folder);
        $deleted = 0;

        foreach ($this->files($folder) as $file) {
            // Never operate on paths that resolved outside the base.
            if (str_starts_with($file['path'], realpath($this->basePath()).DIRECTORY_SEPARATOR)) {
                if (@unlink($file['path'])) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }

    /**
     * Delete log files older than the given number of days (mtime based).
     *
     * @return int number of files deleted
     */
    public function pruneFilesOlderThan(string $folder, int $days): int
    {
        $cutoff = time() - ($days * 86400);
        $deleted = 0;

        foreach ($this->files($folder) as $file) {
            $mtime = (int) @filemtime($file['path']);
            if ($mtime > 0 && $mtime < $cutoff) {
                if (@unlink($file['path'])) {
                    $deleted++;
                }
            }
        }

        return $deleted;
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
     * @param  bool  $searchStack  when true the search term also matches the
     *                             context and stack trace of each entry.
     * @return array<int, array<string, mixed>>
     */
    public function parse(string $path, ?string $search = null, ?string $level = null, ?string $dateFrom = null, ?string $dateTo = null, bool $searchStack = false): array
    {
        if (! is_file($path) || $this->exceedsSizeLimit($path)) {
            return [];
        }

        $cacheKey = 'log-viewer.parse.'.md5($path).'.'.(int) @filemtime($path);
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
            $entries = array_values(array_filter(
                $entries,
                fn ($e) => str_contains(mb_strtolower($e['message']), $needle)
                    || ($searchStack
                        && (str_contains(mb_strtolower((string) $e['context']), $needle)
                            || str_contains(mb_strtolower((string) $e['stack']), $needle)))
            ));
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
     * Compare the entries of two date ranges (A and B) from the same file.
     *
     * Entries are matched by their stable content hash so the same log line
     * (message + context + stack) present in both ranges counts as "common".
     * The result distinguishes lines that only appear in range A vs. only in B
     * and reports totals for each side.
     *
     * @param  bool  $searchStack  when true the search term also matches the
     *                             context and stack trace of each entry.
     * @return array<string, mixed>
     */
    public function diff(
        string $path,
        ?string $fromA = null,
        ?string $toA = null,
        ?string $fromB = null,
        ?string $toB = null,
        ?string $level = null,
        ?string $search = null,
        bool $searchStack = false,
    ): array {
        $all = $this->parse($path, $search, $level, null, null, $searchStack);

        // Keep only entries inside each range (parse() filters by date too, but
        // re-applying here keeps the two ranges independent of the component state).
        $rangeA = array_values(array_filter(
            $all,
            fn ($e) => $this->inRange($e['date'], $fromA, $toA)
        ));
        $rangeB = array_values(array_filter(
            $all,
            fn ($e) => $this->inRange($e['date'], $fromB, $toB)
        ));

        // Distinct lines per range, keyed by content hash.
        $byHashA = $this->uniqueByHash($rangeA);
        $byHashB = $this->uniqueByHash($rangeB);

        $added = [];   // lines only present in range B ("nuevas" vs A)
        $removed = []; // lines only present in range A ("desaparecidas" en B)
        foreach ($byHashB as $hash => $entry) {
            if (! isset($byHashA[$hash])) {
                $added[] = $entry;
            }
        }
        foreach ($byHashA as $hash => $entry) {
            if (! isset($byHashB[$hash])) {
                $removed[] = $entry;
            }
        }

        $sort = fn (array &$list) => usort($list, fn ($a, $b) => strcmp($a['date'], $b['date']));

        // Common = lines whose hash appears in both ranges (one representative).
        $commonEntries = [];
        foreach ($byHashA as $hash => $entry) {
            if (isset($byHashB[$hash])) {
                $commonEntries[] = $entry;
            }
        }
        $sort($commonEntries);
        $sort($added);
        $sort($removed);

        return [
            'rangeA' => [
                'from' => $fromA,
                'to' => $toA,
                'total' => count($rangeA),
                'distinct' => count($byHashA),
            ],
            'rangeB' => [
                'from' => $fromB,
                'to' => $toB,
                'total' => count($rangeB),
                'distinct' => count($byHashB),
            ],
            'added' => $added,
            'removed' => $removed,
            'common' => count($commonEntries),
            'commonEntries' => $commonEntries,
        ];
    }

    /**
     * Whether a date string (YYYY-MM-DD HH:MM:SS) falls inside an inclusive range.
     */
    private function inRange(string $date, ?string $from, ?string $to): bool
    {
        $day = substr($date, 0, 10);

        if ($from && $day < $from) {
            return false;
        }
        if ($to && $day > $to) {
            return false;
        }

        return true;
    }

    /**
     * Collapse a list of entries into a map keyed by content hash (same line).
     *
     * @return array<string, array<string, mixed>>
     */
    private function uniqueByHash(array $entries): array
    {
        $map = [];

        foreach ($entries as $entry) {
            // Prefer the first occurrence of each distinct line.
            $map[$this->contentHash($entry)] ??= $entry;
        }

        return $map;
    }

    /**
     * Content-only hash used for diffing, ignoring the timestamp so the same
     * log line on a different day is still recognized as "common".
     *
     * @param  array<string, mixed>  $entry
     */
    private function contentHash(array $entry): string
    {
        return hash('sha1', implode('\n', [
            $entry['level'],
            $entry['message'],
            $entry['context'],
            $entry['stack'],
        ]));
    }

    /**
     * Compute a count summary grouped by level.
     *
     * @param  array<int, array<string, mixed>>  $logs
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
            'name' => basename($path),
            'path' => $path,
            'size' => (int) @filesize($path),
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
        $lines = preg_split('/\r\n|\r|\n/', $content);

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
                $chunks[count($chunks) - 1] .= "\n".$line;
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
                'level' => 'INFO',
                'date' => '',
                'env' => '',
                'message' => trim($chunk),
                'context' => '',
                'stack' => '',
                'hash' => hash('sha1', $chunk),
            ];
        }

        $date = $m[1];
        $env = $m[2];
        $level = $m[3];
        $message = trim($m[4]);

        // Split inline JSON context off the message if present.
        $context = '';
        if (preg_match('/\s+(\{.*\})\s*$/', $message, $cm)) {
            $context = $cm[1];
            $message = trim(substr($message, 0, -(strlen($cm[1]) + 1))); // also drop leading space
            $message = rtrim($message, ' ');
        }

        return [
            'level' => $level,
            'date' => $date,
            'env' => $env,
            'message' => $message,
            'context' => $context,
            'stack' => trim($rest),
            'hash' => hash('sha1', $chunk),
        ];
    }
}
