<?php
namespace App\Http\Livewire\Movile\Representant;

use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Lapso;
use App\Services\SendPulseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jenssegers\Date\Date;
use Livewire\Component;

class IndexComponent extends Component
{
    public $representant, $lapsos, $tab = 'details';

    public function mount()
    {
        $user               = Auth::user();
        $this->representant = $user->representant;
        $this->lapsos       = Lapso::all();
    }

    public function confirmProsecution($estudiantId)
    {
        $estudiant = Estudiant::where('id', $estudiantId)
            ->where('representant_id', $this->representant->id)
            ->where('status_active', 'true')
            ->first();

        if ($estudiant) {
            $estudiant->update([
                'status_prosecution' => true,
                'date_prosecution'   => now(),
            ]);

            // Refresh the representant data to update the lists
            $this->representant->refresh();

            // Notify via email
            $this->sendNotification($estudiant);

            $this->dispatchBrowserEvent('swal:modal', [
                'type'  => 'success',
                'title' => 'Continuidad Confirmada',
                'text'  => 'La prosecución de ' . $estudiant->shortname . ' ha sido confirmada exitosamente.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.movile.representant.index-component');
    }

    private function sendNotification($estudiant)
    {
        try {
            $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
            $autoridad   = Autoridad::getTipoAuthority('1'); // DIRECTOR ACADÉMICO / GENERAL
            $director    = Autoridad::getTipoAuthority('2');
            $toDate      = Date::now()->format('d F Y');

            $subject = 'Confirmación de Continuidad Escolar - ' . $estudiant->fullname;

            $htmlContent = view('email.prosecuicion.index', [
                'institucion'  => $institucion,
                'autoridad'    => $autoridad,
                'director'     => $director,
                'estudiant'    => $estudiant,
                'representant' => $this->representant,
                'toDate'       => $toDate,
            ])->render();

            $sendPulseService = app(SendPulseService::class);

            // Recipients as requested:
            // To: representant email
            // CC: env('MAIL_CC_ADDRESS_CONTROL')
            // BCC: env('MAIL_CC_ADDRESS')

            $to  = $this->representant->email;
            $cc  = env('MAIL_CC_ADDRESS_CONTROL');
            $bcc = env('MAIL_CC_ADDRESS');

            if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $sendPulseService->send(
                    $to,
                    $subject,
                    $htmlContent,
                    null,
                    true, // Using queue
                    $cc,
                    $bcc
                );
            }
        } catch (\Exception $e) {
            Log::error('Error sending prosecution notification: ' . $e->getMessage());
        }
    }
}
