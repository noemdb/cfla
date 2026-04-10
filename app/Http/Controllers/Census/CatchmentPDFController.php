<?php

namespace App\Http\Controllers\Census;

use App\Http\Controllers\Controller;
use App\Models\app\Academy\Catchment;
use App\Models\app\Academy\Enrollment;
use App\Models\app\Entity\Autoridad;
use App\Models\app\Entity\Institucion;
use Illuminate\Http\Request;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CatchmentPDFController extends Controller
{
    public function downloadPDF($token)
    {
        $catchment = Catchment::where('token', $token)->firstOrFail();
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
            'grado' => $catchment->grado,
            'institution' => $institution,
            'autoridad1' => $autoridad1,
            'autoridad2' => $autoridad2,
            'qrCode' => $this->generateQrCodePDF($token),
        ];

        // Crear el PDF
        $pdf = Pdf::loadView('pdfs.catchment-form', $data);

        // Guardar temporalmente el archivo
        $filename = 'formato_registro_censo_' . time() . '_' . $catchment->representant_ci . '.pdf';
        Storage::disk('public')->put('pdfs/' . $filename, $pdf->output());

        return response()->download(storage_path('app/public/pdfs/' . $filename));
    }

    public function preview()
    {
        $catchment = Catchment::first();
        $institution = Institucion::first();
        $autoridad1 = Autoridad::getAuthority('DIRECTOR GENERAL Y ADMINISTRATIVO');
        $autoridad2 = Autoridad::getAuthority('DIRECTORA');

        if (!$catchment) {
            // Mock data if no catchment exists
            $data = [
                'firstname' => 'JUAN',
                'lastname' => 'PEREZ',
                'date_birth' => '2015-05-20',
                'representant_name' => 'MARIA PEREZ',
                'representant_ci' => '12345678',
                'representant_phone' => '0414-1234567',
                'representant_cellphone' => '0424-7654321',
                'day_appointment' => '2026-05-15',
                'grado' => (object)['name' => '1ER GRADO'],
                'institution' => $institution ?? (object)['name' => 'COLEGIO FRAY LUIS AMIGÓ'],
                'autoridad1' => $autoridad1 ?? (object)['profile_professional' => 'DR.', 'fullname' => 'JUAN DIRECTOR', 'position' => 'DIRECTOR GENERAL'],
                'autoridad2' => $autoridad2 ?? (object)['profile_professional' => 'MGTR.', 'fullname' => 'MARIA DIRECTORA', 'position' => 'DIRECTORA'],
                'qrCode' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
            ];
        } else {
            $data = [
                'firstname' => $catchment->firstname,
                'lastname' => $catchment->lastname,
                'date_birth' => $catchment->date_birth,
                'representant_name' => $catchment->representant_name,
                'representant_ci' => $catchment->representant_ci,
                'representant_phone' => $catchment->representant_phone,
                'representant_cellphone' => $catchment->representant_cellphone,
                'day_appointment' => $catchment->day_appointment,
                'grado' => $catchment->grado,
                'institution' => $institution,
                'autoridad1' => $autoridad1,
                'autoridad2' => $autoridad2,
                'qrCode' => $this->generateQrCodePDF($catchment->token),
            ];
        }

        return view('pdfs.catchment-form', $data);
    }

    public function generateQrCodePDF($token)
    {
        Catchment::where('token', $token)->firstOrFail();
        $pdfUrl = route('catchment.download.pdf',$token); // Ruta que descarga el PDF
        return 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(200)->generate($pdfUrl));
    }
}