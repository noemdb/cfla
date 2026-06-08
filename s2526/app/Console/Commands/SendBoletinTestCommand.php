<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\app\Estudiante\Representant;
use App\Jobs\EnviarInformeNotasJob;

class SendBoletinTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boletin:send-test {cedula} {email} {lapso=1}';
    // php artisan boletin:send-test cedula=10857718 email=noemdb@gmail.com --lapso=1

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía el boletín de notas de los representados de una cédula específica a un email de prueba';

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
        $cedula = $this->argument('cedula');
        $email = $this->argument('email');
        $lapsoId = $this->argument('lapso');

        $this->info("Buscando representante con cédula: {$cedula}");

        $representant = Representant::where('ci_representant', $cedula)->first();

        if (!$representant) {
            $this->error("No se encontró ningún representante con la cédula {$cedula}");
            return 1;
        }

        $this->info("Representante encontrado: {$representant->name} {$representant->lastname}");

        $estudiants = $representant->estudiants;

        if ($estudiants->isEmpty()) {
            $this->warn("El representante no tiene estudiantes asociados.");
            return 0;
        }

        $this->info("Encontrados " . $estudiants->count() . " estudiantes.");

        foreach ($estudiants as $estudiant) {
            $this->info("Despachando job para estudiante: {$estudiant->name} {$estudiant->lastname} (ID: {$estudiant->id}) al email: {$email}");

            EnviarInformeNotasJob::dispatch($estudiant->id, $lapsoId, $email);
        }

        $this->info("Todos los jobs han sido despachados exitosamente.");

        return 0;
    }
}
