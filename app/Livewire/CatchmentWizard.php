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
use Illuminate\Support\Facades\Storage;

class CatchmentWizard extends Component
{
    use Actions;

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
    public $grado_id; // Paso 3: Grado/Nivel solicitado

    // Validaciones por paso
    protected $rules = [
        'email' => 'required|email',
        'input_code' => 'required|size:6',
        'firstname' => 'required|string|max:100',
        'lastname' => 'required|string|max:100',
        'date_birth' => 'required|date|before:today',
        'representant_ci' => 'required|numeric',
        'representant_name' => 'required|string|max:100',
        'representant_phone' => 'required|numeric|digits:12',
        'representant_cellphone' => 'required|numeric|digits:12',
        'grado_id' => 'required|integer',
    ];

    // public function mount()
    // {
    //     $this->email="noemdb@gmail.com";
    //     $this->representant_ci="14608133";
    //     $this->representant_name="noe dominguez";
    //     $this->representant_phone="584121234567";
    //     $this->representant_cellphone="584121345678";
    //     $this->grado_id="11";
    //     $this->firstname="camila andreina".rand(1,1000);
    //     $this->lastname="dominguez".rand(1,1000);
    //     $this->date_birth="2025-01-".rand(1,28);
    // }

    // Paso 1: Enviar código al email
    public function sendEmailCode()
    {
        $this->validate(['email' => 'required|email']);

        // Generar un código aleatorio
        $this->verificationCode = rand(100000, 999999);
        $this->input_code = $this->verificationCode;

        Session::put('email_code', $this->verificationCode); // Guardar en sesión
        
        // Mail::raw("Tu código de validación es: $this->verificationCode", function ($message) {
        //     $message->to($this->email)
        //             ->subject('Código de verificación');
        // });

        $this->notification()->success(
            $title = 'Excelente!',
            $description = 'Se ha enviado un código de validación a tu correo.'
        );
    }

    // Validar código ingresado
    public function validateEmailCode()
    {
        if ($this->input_code == Session::get('email_code')) {
            $this->currentStep = 2;
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
                    ->exists()) {
            $this->notification()->error(
                $title = 'Error !!!',
                $description = 'Este estudiante ya está registrado.'
            );            
        } else {
            $this->currentStep = 3;
        }
    }

    // Paso 4: Guardar inscripción
    public function saveEnrollment()
    {
        $this->validate();

        // $bytes = random_bytes(5); // Genera 5 bytes aleatorios
        // $token = substr(str_pad(abs(hexdec(bin2hex($bytes))), 10, '0', STR_PAD_LEFT), 0, 10);

        $time = Carbon::now()->timestamp;
        $random = mt_rand(10000, 99999);
        $token = substr($time . $random, -10);

        $catchment = Catchment::create([
            'user_id' => auth()->id(),
            'email' => $this->email,
            'representant_ci' => $this->representant_ci,
            'representant_name' => $this->representant_name,
            'representant_phone' => $this->representant_phone,
            'representant_cellphone' => $this->representant_cellphone,
            'grado_id' => $this->grado_id,
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
            'grado_id' => $catchment->grado_id,
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
        $pdfUrl = route('catchment.download.pdf',$catchment->token); // Ruta que descarga el PDF
        return QrCode::size(200)->generate($pdfUrl);
    }

    public function generateQrCodePDF($token)
    {
        Catchment::where('token', $token)->firstOrFail();
        $pdfUrl = route('catchment.download.pdf',$token); // Ruta que descarga el PDF
        return 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(200)->generate($pdfUrl));
    }

    public function render()
    {
        return view('livewire.catchment-wizard');
    }
}
