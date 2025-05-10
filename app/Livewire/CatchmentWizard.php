<?php

namespace App\Livewire;

use App\Models\app\Academy\Catchment;
use App\Models\app\Entity\Autoridad;
use App\Models\app\Entity\Institucion;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use WireUi\Traits\Actions;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CatchmentWizard extends Component
{
    use Actions;
    use CatchmentValidate;
    use CatchmentUpdates;

    public $catchment_id; // ID de la censo
    public $currentStep = 1; // Paso actual del asistente
    public $email; // Paso 1: Correo electrónico
    public $verificationCode = null; // Código de verificación
    public $input_code; // Código generado para validar
    public $firstname; // Paso 2: Nombre completo del niño/a
    public $lastname; // Paso 2: Nombre completo del niño/a
    public $date_birth; // Paso 2: Fecha de nacimiento
    public $representant_name; // Paso 3: Nombre del representante
    public $representant_ci; // Paso 3: CI del representante
    public $representant_phone; // Paso 3: WhatsApp del representante
    public $representant_cellphone; // Paso 3: Nombre del representante
    public $grade; // Paso 3: Grado/Nivel solicitado
    public $day_appointment; // Dia de la cita
    public $day_appointment_start = '2025-05-19'; // Dia de la cita inical
    public $day_appointment_end = '2025-05-21'; // Dia de la cita final
    public $status_validate_code_email; // Dia de la cita final

    protected $listeners = ['hideVideo'];
    public bool $showVideo = true; // Estado inicial: mostrar video
    public function hideVideo()
    {
        $this->showVideo = false; // Ocultar video al finalizar
    }

    public $isVideoLoaded = false;
    public function videoLoaded()
    {
        $this->isVideoLoaded = true;
    }

    public function setStep($step)
    {
        $this->currentStep = ($this->status_validate_code_email) ? $step : $this->currentStep;
    }

    public function restart()
    {
        $this->currentStep = 1;
        $this->catchment_id = null;
        $this->email = null;
        $this->verificationCode = null;
        $this->input_code = null;
        $this->firstname = null;
        $this->lastname = null;
        $this->date_birth = null;
        $this->representant_name = null;
        $this->representant_ci = null;
        $this->representant_phone = null;
        $this->representant_cellphone = null;
        $this->grade = null;
        $this->day_appointment = null;
        $this->status_validate_code_email = null;
    }

    public function mount()
    {
        $today = now()->toDateString();
        $this->day_appointment = $today;
        $this->day_appointment_start = ($today > $this->day_appointment_start) ? $today : $this->day_appointment_start;
    }

    // Paso 1: Enviar código al email
    public function sendEmailCode()
    {
        abort(500);

        $this->validate(['email' => 'required|email']);

        // Generar un código aleatorio
        $this->verificationCode = rand(100000, 999999);

        try {
            Mail::raw("Tu código de validación es: {$this->verificationCode}", function ($message) {
                $message->to($this->email)
                    ->subject('Código de verificación');
            });

            Session::put('email_code', $this->verificationCode);

            $this->notification()->success(
                $title = 'Excelente!',
                $description = 'Se ha enviado un código de validación a tu correo.'
            );
        } catch (\Exception $e) {
            dd($e);
            $this->verificationCode = null;
            $this->notification()->error(
                $title = 'Error !!!',
                $description = 'No se pudo enviar el correo. Por favor, intenta nuevamente.'
            );
        }
    }

    // Validar código ingresado
    public function validateEmailCode()
    {
        if ($this->input_code == Session::get('email_code')) {
            $this->currentStep = 2;
            $this->status_validate_code_email = true;
            $this->notification()->success(
                $title = 'Excelente!',
                $description = 'Tu correo electrónico ha sido validado correctamente. Ahora podremos mantenerte informado y comunicarnos contigo efectivamente'
            );
        } else {
            $this->notification()->error(
                $title = 'Error !!!',
                $description = 'Código incorrecto.'
            );
        }
    }

    public function validateStudent()
    {
        $this->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'date_birth' => 'required|date|before:today',
        ]);

        // Verificar si ya está registrado
        if (Catchment::where('firstname', $this->firstname)
            ->where('lastname', $this->lastname)
            ->where('date_birth', $this->date_birth)
            ->exists()
        ) {
            $this->notification()->error(
                $title = 'Error !!!',
                $description = 'Este estudiante ya está registrado.'
            );
        } else {
            $this->currentStep = 3;
        }
    }

    // Paso 4: Guardar inscripción
    public function saveCatchment()
    {
        $date = $this->validate();

        $time = Carbon::now()->timestamp;
        $random = mt_rand(10000, 99999);
        $token = substr($time . $random, -10);

        $catchment = Catchment::create([
            'user_id' => auth()->id(),
            'email' => $this->email,
            'day_appointment' => $this->day_appointment,
            'representant_ci' => $this->representant_ci,
            'representant_name' => $this->representant_name,
            'representant_phone' => $this->representant_phone,
            'representant_cellphone' => $this->representant_cellphone,
            'grade' => $this->grade,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'date_birth' => $this->date_birth,
            'token' => $token,
        ]);

        $this->catchment_id = ($catchment) ? $catchment->id : null;
        $this->notification()->success(
            $title = 'Excelente!',
            $description = 'Registro realizado con éxito.'
        );
        // return redirect()->route('home'); // Redirigir al usuario

        $this->currentStep = 4;
    }

    public function updatedDayAppointment($value)
    {
        $this->day_appointment = $value;
        $this->validate([
            'day_appointment' => 'required|after_or_equal:' . $this->day_appointment_start . '|before_or_equal:' . $this->day_appointment_end . '',
        ]);
    }

    public function downloadPDF($catchment_id)
    {
        $catchment = Catchment::findOrFail($catchment_id);
        $institution = Institucion::first();
        $autoridad1 = Autoridad::getAuthority('DIRECTOR GENERAL Y ADMINISTRATIVO');
        $autoridad2 = Autoridad::getAuthority('DIRECTORA'); //dd($institution,$autoridad1,$autoridad2);
        $data = [
            'firstname' => $catchment->firstname,
            'lastname' => $catchment->lastname,
            'date_birth' => $catchment->date_birth,
            'representant_name' => $catchment->representant_name,
            'representant_ci' => $catchment->representant_ci,
            'representant_phone' => $catchment->representant_phone,
            'representant_cellphone' => $catchment->representant_cellphone,
            'day_appointment' => $catchment->day_appointment,
            // 'grado_id' => $catchment->grado_id,
            'grado' => $catchment->grado,
            'institution' => $institution,
            'autoridad1' => $autoridad1,
            'autoridad2' => $autoridad2,
            'qrCode' => $this->generateQrCodePDF($catchment->token),
        ];

        // Crear el PDF
        $pdf = Pdf::loadView('pdfs.catchment-form', $data);

        // Guardar temporalmente el archivo
        $filename = 'formato_registro_' . time() . '.pdf';
        Storage::disk('public')->put('pdfs/' . $filename, $pdf->output());

        return response()->download(storage_path('app/public/pdfs/' . $filename));
    }

    public function generateQrCode($catchment_id)
    {
        $catchment = Catchment::findOrFail($catchment_id);
        $pdfUrl = route('catchment.download.pdf', $catchment->token); // Ruta que descarga el PDF
        return QrCode::size(200)->generate($pdfUrl);
    }

    public function generateQrCodePDF($token)
    {
        Catchment::where('token', $token)->firstOrFail();
        $pdfUrl = route('catchment.download.pdf', $token); // Ruta que descarga el PDF
        return 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(200)->generate($pdfUrl));
    }

    public function render()
    {
        return view('livewire.catchment-wizard');
    }
}
