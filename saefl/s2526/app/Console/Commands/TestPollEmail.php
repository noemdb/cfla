<?php
namespace App\Console\Commands;

use App\Jobs\Email\PollMain\ProcessNotifyAttendee;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Poll\PollMain;
use Illuminate\Console\Command;
use Jenssegers\Date\Date;

class TestPollEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poll:test-email {email} {--poll= : ID of the poll to use}';
    // php artisan poll:test-email noemdb@gmail.com

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Poll email sending via SendPulseService';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email  = $this->argument('email');
        $pollId = $this->option('poll');

        $this->info("Iniciando prueba de envío de email de encuesta para: {$email}");

        // 1. Obtener la encuesta
        if ($pollId) {
            $poll_main = PollMain::find($pollId);
        } else {
            $poll_main = PollMain::latest()->first();
        }

        if (! $poll_main) {
            $this->error("No se encontró ninguna encuesta.");
            return 1;
        }

        $this->info("Usando encuesta: [{$poll_main->id}] {$poll_main->title}");

        // 2. Obtener datos básicos
        $institucion    = Institucion::OrderBy('created_at', 'DESC')->first();
        $director       = Autoridad::getTipoAuthority('2');
        $toDate         = Date::now()->format('d F Y');
        $poll_questions = $poll_main->poll_questions;

        // 3. Obtener un participante real para simular los datos, pero sobreescribir el email
        $poll_token = $poll_main->poll_tokens()->first();
        if (! $poll_token) {
            $this->error("La encuesta no tiene tokens generados. Genera tokens primero.");
            return 1;
        }

        $attendee = $poll_token->user;
        if (! $attendee) {
            $this->error("El token no tiene un usuario asociado.");
            return 1;
        }

        // 4. Preparar el array de datos (Idéntico al de PollController)
        $dataEmail = [
            // 'mailCCAddress'  => env('MAIL_CC_ADDRESS_ADMON', 'soporte.saefl@gmail.com'),
            'mailCCAddress'  => 'soporte.saefl@gmail.com',
            'subject'        => 'PRUEBA: Invitación a participar en un proceso de consultas',
            'address'        => $email, // Usamos el email proporcionado por el usuario
            'poll_main'      => $poll_main,
            'poll_questions' => $poll_questions,
            'poll_token'     => $poll_token,
            'attendee'       => $attendee,
            'institucion'    => $institucion,
            'director'       => $director,
            'toDate'         => $toDate,
            'view'           => 'email.polls.notify',
        ];

        $this->info("Despachando ProcessNotifyAttendee con SendPulseService...");

        // 5. Despachar el job (que ahora usa SendPulseService)
        ProcessNotifyAttendee::dispatch($dataEmail);

        $this->info("✅ Job despachado correctamente. Revisa los logs o la bandeja de entrada.");

        return 0;
    }
}
