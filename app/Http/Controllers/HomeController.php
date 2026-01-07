<?php

namespace App\Http\Controllers;

use App\Models\app\Academy\Enrollment;
use App\Models\app\Academy\Profesor;
use App\Models\app\Blog\Post;
use App\Models\app\Entity\Autoridad;
use App\Models\app\Entity\Institucion;
use App\Models\app\Learner\Estudiant;
use App\Models\app\Learner\Representant;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Google_Service_Gmail;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class HomeController extends Controller
{

    public function diagnostico(Request $request)
    {
        return view('diagnostico');
    }

    public function studia(Request $request)
    {
        $testimonials = collect();
        $faqs = collect();

        return view('studia', compact('testimonials', 'faqs'));
    }

    public function welcome(Request $request)
    {
        return view('welcome');
    }

    public function home(Request $request)
    {
        return view('home');
    }

    public function payment(Request $request)
    {
        return view('payment');
    }

    public function enrollment(Request $request)
    {
        return view('enrollment');
    }

    public function credicard(Request $request)
    {
        return view('credicard');
    }

    public function post($id)
    {
        $post = Post::findOrFail($id);
        return view('post', compact('post'));
    }

    public function prosecucion(Request $request)
    {
        return view('prosecucion');
    }

    // Actualizar el método para usar ID en lugar de token
    public function downloadProsecucionPDF($id)
    {
        // Buscar los estudiantes por el ID del representante
        $representant = Representant::findOrFail($id);
        $estudiants = Estudiant::where('representant_id', $representant->id)
            ->where('status_prosecution', true)
            ->get();

        if ($estudiants->isEmpty()) {
            abort(404, 'No se encontraron estudiantes para prosecución');
        }

        $time = Carbon::now();
        $toDate = Carbon::now()->format('d F Y');

        $institution = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2'); //director
        $autoridad2 = Autoridad::getTipoAuthority('4'); //ADMINISTRADOR

        $data = [
            'representant' => $representant,
            'estudiants' => $estudiants,
            'institution' => $institution,
            'autoridad1' => $autoridad1,
            'autoridad2' => $autoridad2,
            'fecha_proceso' => Carbon::now()->format('d/m/Y'),
            'qrCode' => $this->generateQrCodePDF($id),
        ];

        // Crear el PDF
        $pdf = Pdf::loadView('pdfs.prosecucion-form', $data);

        // Guardar temporalmente el archivo
        $filename = 'prosecucion_' . $representant->ci_representant . '_' . time() . '.pdf';
        Storage::disk('public')->put('pdfs/' . $filename, $pdf->output());

        return response()->download(storage_path('app/public/pdfs/' . $filename));
    }

    public function generateQrCodePDF($id)
    {
        $pdfUrl = route('prosecucion.download.pdf', $id);
        return 'data:image/png;base64,' . base64_encode(QrCode::format('png')->size(200)->generate($pdfUrl));
    }

    public function prosecucion_guia(Request $request)
    {
        return view('prosecucion_guia');
    }
}
