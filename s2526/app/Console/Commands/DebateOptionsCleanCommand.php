<?php
namespace App\Console\Commands;

use App\Models\app\Educational\DebateOption;
use Illuminate\Console\Command;

class DebateOptionsCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debate:clean-options {--dry : Realiza una simulación sin modificar la base de datos}';
    //php artisan debate:clean-options --dry

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia los prefijos (a), b., etc) de las opciones de debate.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDry = $this->option('dry');

        if ($isDry) {
            $this->info("MODO DRY ACTIVO: Las opciones no serán modificadas en la base de datos.");
        }

        $this->info("Buscando opciones con prefijos (A. B) C. d)...");

        // Obtenemos los que coincidan con la forma exacta (a) o a.) ignorando si son mayúsculas o minúsculas.
        $options = DebateOption::where('text', 'regexp', '^[[:space:]]*[A-Da-d][\.\)][[:space:]]*')->get();

        if ($options->isEmpty()) {
            $this->info("No se encontraron opciones para limpiar. ¡Todo en orden!");
            return 0;
        }

        $count = 0;
        $bar   = $this->output->createProgressBar(count($options));
        $bar->start();

        foreach ($options as $opt) {
            $original = trim($opt->text);
            // Eliminar la "a)", "B.", "c )", etc. al inicio (case insensitive)
            $clean = preg_replace('/^\s*[a-d][\.\)]\s*/i', '', $original);

            if ($clean !== $original) {
                $opt->text = $clean;

                if (! $isDry) {
                    $opt->save();
                }

                $count++;
            }
            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        if ($count > 0) {
            if ($isDry) {
                $this->info("Simulación terminada. Se habrían corregido {$count} opciones.");
            } else {
                $this->info("Operación exitosa. Se corrigieron {$count} opciones.");
            }
        } else {
            $this->info("Las opciones que coincidían con el regexp ya estaban corregidas.");
        }

        return 0;
    }
}
