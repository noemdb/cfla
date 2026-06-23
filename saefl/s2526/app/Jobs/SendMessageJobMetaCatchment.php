<?php

namespace App\Jobs;

use App\Models\app\Enrollment\Catchment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FacebookTokenService;
use Illuminate\Support\Facades\Log;

class SendMessageJobMetaCatchment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ident;
    protected $phone;

    /**
     * Create a new job instance.
     */
    public function __construct($ident, $phone)
    {
        $this->ident = $ident;
        $this->phone = $phone;
    }

    /**
     * Execute the job.
     */
    public function handle(FacebookTokenService $tokenService)
    {
        // Buscar el registro correspondiente al identificador
        $catchment = Catchment::where('representant_ci', $this->ident)->first();

        if (! $catchment) {
            Log::error("Registro no encontrado para CI: {$this->ident}");
            return;
        }

        // Llamar al método que envía el mensaje
        app()->call('App\Http\Controllers\Administracion\Email\Catchment\CatchmentSendNotificationsController@sendMessegeMetaTemplateGeneral', [
            'tokenService' => $tokenService,
            'ident' => $this->ident,
            'phone' => $this->phone,
        ]);

        Log::info("Mensaje enviado para CI: {$this->ident}");
    }
}