<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Lms\LmsMediaLibrary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupLmsMedia extends Command
{
    protected $signature = 'lms:cleanup-media
                          {--dry-run : Solo listar, no eliminar}';
    protected $description = 'Elimina archivos de media_library sin referencias activas';

    public function handle(): int
    {
        $orphaned = LmsMediaLibrary::whereNull('deleted_at')
            ->whereDoesntHave('contents')
            ->whereDoesntHave('resources')
            ->get();

        if ($orphaned->isEmpty()) {
            $this->info('No se encontraron archivos huérfanos.');
            return Command::SUCCESS;
        }

        $this->warn("{$orphaned->count()} archivo(s) sin referencias:");

        foreach ($orphaned as $media) {
            $this->line("  [{$media->id}] {$media->original_name} ({$media->size_for_humans})");

            if (!$this->option('dry-run') && $media->isLocal()) {
                Storage::disk($media->disk)->delete($media->path);
                $media->delete();
            }
        }

        if (!$this->option('dry-run')) {
            $this->info("{$orphaned->count()} archivo(s) eliminados.");
        }

        return Command::SUCCESS;
    }
}
