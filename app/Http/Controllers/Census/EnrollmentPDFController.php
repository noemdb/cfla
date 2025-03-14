<?php

namespace App\Http\Controllers\Census;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Enrollment;
use App\Models\app\Entity\Autoridad;
use App\Models\app\Entity\Institucion;
use Illuminate\Http\Request;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class EnrollmentPDFController extends Controller
{
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
            'ci_representant' => $emrollment->ci_representant,
            'phone_representant' => $emrollment->phone_representant,
            'cellphone_representant' => $emrollment->cellphone_representant,
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
