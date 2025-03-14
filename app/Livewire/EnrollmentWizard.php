<?php

namespace App\Livewire;

use App\Models\app\Academy\Enrollment;
use App\Models\app\Entity\Autoridad;
use App\Models\app\Entity\Institucion;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use WireUi\Traits\Actions;

use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class EnrollmentWizard extends Component
{
    use Actions;

    public $emrollment_id; // ID de la censo
    public $currentStep = 1; // Paso actual del asistente
    public $email; // Paso 1: Correo electrónico
    public $verificationCode = null; // Código de verificación
    public $input_code; // Código generado para validar
    public $name; // Paso 2: Nombre completo del niño/a
    public $lastname; // Paso 2: Nombre completo del niño/a
    public $date_birth; // Paso 2: Fecha de nacimiento
    public $name_representant; // Paso 3: Nombre del representante
    public $lastname_representant; // Paso 3: Nombre del representante
    public $ci_representant; // Paso 3: CI del representante
    public $phone_representant; // Paso 3: WhatsApp del representante
    public $grado_id; // Paso 3: Grado/Nivel solicitado

    // Validaciones por paso
    protected $rules = [
        'email' => 'required|email',
        'input_code' => 'required|size:6',
        'name' => 'required|string|max:100',
        'lastname' => 'required|string|max:100',
        'date_birth' => 'required|date|before:today',
        'ci_representant' => 'required|numeric',
        'name_representant' => 'required|string|max:100',
        'lastname_representant' => 'required|string|max:100',
        'phone_representant' => 'required|numeric|digits:12',
        'grado_id' => 'required|integer',
    ];

    public function mount()
    {
        $this->email="noemdb@gmail.com";
        $this->ci_representant="14608133";
        $this->name_representant="noe";
        $this->lastname_representant="dominguez";
        $this->phone_representant="584121234567";
        $this->grado_id="11";
        $this->name="camila andreina".rand(1,1000);
        $this->lastname="dominguez".rand(1,1000);
        $this->date_birth="2025-01-".rand(1,28);
    }

    // Paso 1: Enviar código al email
    public function sendEmailCode()
    {
        $this->validate(['email' => 'required|email']);

        // Generar un código aleatorio
        $this->verificationCode = rand(100000, 999999);
        // $this->input_code = $this->verificationCode;

        Session::put('email_code', $this->verificationCode); // Guardar en sesión
        
        Mail::raw("Tu código de validación es: $this->verificationCode", function ($message) {
            $message->to($this->email)
                    ->subject('Código de verificación');
        });

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
            'name' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'date_birth' => 'required|date|before:today',
        ]);

        // Verificar si ya está registrado
        if (Enrollment::where('name', $this->name)
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

        $enrollment = Enrollment::create([
            'user_id' => auth()->id(),
            'email_representant' => $this->email,
            'ci_representant' => $this->ci_representant,
            'name_representant' => $this->name_representant,
            'lastname_representant' => $this->lastname_representant,
            'phone_representant' => $this->phone_representant,
            'grado_id' => $this->grado_id,
            'name' => $this->name,
            'lastname' => $this->lastname,
            'date_birth' => $this->date_birth,
        ]);

        $this->emrollment_id = ($enrollment) ? $enrollment->id : null;
        $this->notification()->success(
            $title = 'Excelente!',
            $description = 'Registro realizado con éxito.'
        );
        // return redirect()->route('home'); // Redirigir al usuario

        $this->currentStep = 4;
    }


    public function downloadPDF($emrollment_id)
    {
        $emrollment = Enrollment::findOrFail($emrollment_id);
        $institution = Institucion::first();
        $autoridad1 = Autoridad::getAuthority('DIRECTOR GENERAL Y ADMINISTRATIVO');
        $autoridad2 = Autoridad::getAuthority('DIRECTORA'); //dd($institution,$autoridad1,$autoridad2);
        $data = [
            'name' => $emrollment->name,
            'lastname' => $emrollment->lastname,
            'date_birth' => $emrollment->date_birth, 
            'name_representant' => $emrollment->name_representant,
            'lastname_representant' => $emrollment->lastname_representant,
            'ci_representant' => $emrollment->ci_representant,
            'phone_representant' => $emrollment->phone_representant,
            'grado_id' => $emrollment->grado_id,
            'institution' => $institution,
            'autoridad1' => $autoridad1,
            'autoridad2' => $autoridad2,
            'qrCode' => $this->generateQrCodePDF($emrollment_id),
        ];

        // Crear el PDF
        $pdf = Pdf::loadView('pdfs.enrollment-form', $data);

        // Guardar temporalmente el archivo
        $filename = 'formato_registro_' . time() . '.pdf';
        Storage::disk('public')->put('pdfs/' . $filename, $pdf->output());

        return response()->download(storage_path('app/public/pdfs/' . $filename));
    }

    public function generateQrCode($emrollment_id)
    {
        Enrollment::findOrFail($emrollment_id);

        $pdfUrl = route('download.pdf',$emrollment_id); // Ruta que descarga el PDF

        return QrCode::size(200)->generate($pdfUrl);
    }

    public function generateQrCodePDF($emrollment_id)
    {
        Enrollment::findOrFail($emrollment_id);
        $pdfUrl = route('download.pdf',$emrollment_id); // Ruta que descarga el PDF
        return 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(200)->generate($pdfUrl));
    }

    public function render()
    {
        return view('livewire.enrollment-wizard');
    }
}
