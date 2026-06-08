<?php

namespace App\Http\Controllers\Bienestar\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Enrollment\Catchment;
use App\Models\app\Enrollment\CatchmentInterview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\ResendEmailService;
use App\Services\SendPulseService;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;

class CatchmentController extends Controller
{
    protected const EMAIL_PROVIDER = 'sendpulse'; // 'resend' o 'sendpulse'
    protected $emailService;
    protected $cc_mail, $bcc_mail;

    public function __construct()
    {
        // Seleccionar el servicio de email basado en la configuración
        $this->emailService = $this->getEmailService();
        $this->cc_mail = env('MAIL_CC_ADDRESS_CONTROL', null);
        $this->bcc_mail = [
            env('MAIL_CC_ADDRESS', null),
            env('MAIL_CC_ADDRESS_BIENESTAR', null),
            // env('MAIL_CC_ADDRESS_TESTER', null),
        ];
    }

    /**
     * Obtiene el servicio de email configurado
     *
     * @param string|null $provider Proveedor de email ('resend' o 'sendpulse')
     * @return ResendEmailService|SendPulseService
     */
    protected function getEmailService(?string $provider = null)
    {
        $provider = $provider ?? self::EMAIL_PROVIDER;

        return match ($provider) {
            'resend' => app(ResendEmailService::class),
            default => app(SendPulseService::class),
        };
    }

    public function index(Request $request)
    {
        $representant_ci = (!empty($request->representant_ci)) ? $request->representant_ci : null;
        $catchments = Catchment::select('catchments.*');
        $catchments = ($representant_ci) ? $catchments->where('representant_ci', $representant_ci) : $catchments;
        $catchments = $catchments->where('status_active', true)->orderBy('created_at', 'desc')->get();
        $list_comment = Catchment::COLUMN_COMMENTS;
        return view('bienestars.matriculations.catchments.index', compact('catchments', 'list_comment', 'representant_ci'));
    }

    public function interviews(Request $request)
    {
        $representant_ci = (!empty($request->representant_ci)) ? $request->representant_ci : null;
        $catchment_interviews = CatchmentInterview::select('catchment_interviews.*');
        $catchment_interviews = ($representant_ci) ? $catchment_interviews->where('identification_number', $representant_ci) : $catchment_interviews;
        $catchment_interviews = $catchment_interviews->orderBy('created_at', 'desc')->get();
        $list_comment = CatchmentInterview::COLUMN_COMMENTS;
        return view('bienestars.matriculations.interviews.index', compact('catchment_interviews', 'list_comment', 'representant_ci'));
    }

    public function edit_interview($id)
    {
        $catchment_interview = CatchmentInterview::findOrfail($id);
        $list_comment = CatchmentInterview::COLUMN_COMMENTS;
        $list_grade = CatchmentInterview::list_grade();
        $list_religions = CatchmentInterview::list_religions();
        return view('bienestars.matriculations.interviews.edit', compact('catchment_interview', 'list_comment', 'list_grade', 'list_religions'));
    }

    public function update_interview(Request $request, $id)
    {
        $request->validate([
            // ...otras reglas...
            'accepted' => [
                'boolean',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->input('status_standby')) {
                        $fail('No puede aceptar y poner en espera una solicitud de nátricula al mismo tiempo.');
                    }
                }
            ],
            'status_standby' => [
                'boolean',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->input('accepted')) {
                        $fail('No se puede poner en espera y aceptar una solicitud de nátricula al mismo tiempo.');
                    }
                }
            ],
        ]);

        $catchment_interview = CatchmentInterview::findOrfail($id);
        $catchment_interview->fill($request->all());
        $catchment_interview->save();
        $message = null;

        if ($catchment_interview->accepted == true) {

            $catchment_interview->generateToken();
            $catchment_interview->save();

            if ($request->status_notify == true) {

                $response = $this->sendAcceptanceEmail($catchment_interview->id, 'sendpulse');
                $result = json_decode($response->getContent(), true);

                if (!$result['success']) {
                    Session::flash('operp_error', 'Error al enviar el correo: ' . $result['message']);
                }
            }

            $response = $this->insertEstudiantRepresentanAccepted($catchment_interview->id);
            $responseData = json_decode($response->getContent(), true);

            if (isset($responseData['message'])) {
                $message = $responseData['message'];
            } else {
                $message = $responseData['error'] ?? 'Error desconocido';
            }
        }

        if ($catchment_interview->status_standby == true) {
            if ($request->status_notify == true) {
                $response = $this->sendStandbyEmail($catchment_interview->id, 'sendpulse');
                $result = json_decode($response->getContent(), true); // Convertir la respuesta JSON a un array

                if (!$result['success']) {
                    Session::flash('operp_error', 'Error al enviar el correo: ' . $result['message']);
                }
            }
        }

        if ($request->status_notify == true) {
            $catchment_interview->status_notified = true;
            $catchment_interview->save();
        }

        $message = trans('db_oper_result.update_ok') . '. ' . $message;
        Session::flash('operp_ok', $message);
        return redirect()->route('bienestars.matriculations.interviews.index');
    }

    public function insertEstudiantRepresentanAccepted($id)
    {
        DB::beginTransaction();
        try {
            $interview = CatchmentInterview::findOrFail($id);

            // ───── Crear o encontrar Representante ─────
            $representant = Representant::firstOrCreate(
                ['ci_representant' => $interview->identification_number],
                [
                    'name' => $interview->full_name,
                    'phone' => $interview->phone_numbers,
                    'whatsapp' => $interview->phone_numbers,
                    'cellphone' => $interview->phone_numbers,
                    'email' => $interview->email,
                    'status_active' => 'true',
                ]
            );
            $representant->save();

            // ───── Crear Estudiante ─────
            $full_name = preg_replace('/\s+/', ' ', trim($interview->student_full_name));
            [$est_name, $est_lastname] = $this->splitName($full_name);
            $est_name = mb_strtoupper($this->normalizeName($est_name), 'UTF-8');
            $est_lastname = mb_strtoupper($this->normalizeName($est_lastname), 'UTF-8');
            $dob = $interview->date_of_birth;

            $estudiant = Estudiant::firstOrNew([
                'name' => $est_name,
                'lastname' => $est_lastname,
                'date_birth' => $dob,
            ]);

            $estudiant->planpago_id = 1; // valor por defecto o dinámico
            $estudiant->grado_inicial_id = $interview->grade_year_aspiring ?? 1;
            $estudiant->seccion_inicial = '1';
            $estudiant->type_ci_id = 1;
            $estudiant->ci_estudiant = 'TEMP-' . Str::random(6); // o generar lógica propia
            $estudiant->ci_estudiant_temp = 'TEMP-' . Str::random(6);
            $estudiant->lastname = $est_lastname;
            $estudiant->name = $est_name;
            $estudiant->gender = 'Masculino'; // asumir valor temporal
            $estudiant->date_birth = $interview->date_of_birth;
            $estudiant->email = $interview->email;
            $estudiant->representant_ci = $representant->ci_representant;
            $estudiant->representant_id = $representant->id;
            $estudiant->status_active = 'true';
            $estudiant->token = Str::uuid();
            $estudiant->status_blacklist = 'false';
            $estudiant->status_notice = 1;
            $estudiant->save();

            DB::commit();

            return response()->json(['message' => 'Registro exitoso [Representante y Estudiante agregado/actualizado]'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al registrar: ' . $e->getMessage()], 500);
        }
    }

    private function splitName($fullName)
    {
        $parts = explode(' ', trim($fullName), 2);
        $first = $parts[0];
        $last = $parts[1] ?? '';
        return [$first, $last];
    }

    private function normalizeName(string $value): string
    {
        return ucwords(strtolower(trim($value)));
    }

    /**
     * Envía correo de aceptación para una entrevista
     */
    public function sendAcceptanceEmail(int $interviewId, ?string $provider = null)
    {
        try {
            $interview = CatchmentInterview::findOrFail($interviewId);

            if (!$interview->accepted) {
                return response()->json([
                    'success' => false,
                    'message' => 'La entrevista no está marcada como aceptada'
                ], 400);
            }

            $institucion = Institucion::orderBy('created_at', 'DESC')->first();
            $autoridad = Autoridad::getTipoAuthority('1'); // DIRECTOR GENERAL
            $director = Autoridad::getTipoAuthority('2'); // DIRECTOR GENERAL
            $toDate = Date::now()->format('d F Y');
            $list_comment = Catchment::COLUMN_COMMENTS;

            $htmlContent = view('email.catchment.accepted', [
                'interview' => $interview,
                'institucion' => $institucion,
                'autoridad' => $autoridad,
                'director' => $director,
                'list_comment' => $list_comment,
                'toDate' => $toDate,
            ])->render();

            $cc = $this->cc_mail;
            $bcc = $this->bcc_mail;
            $subject = 'Aceptación de Solicitud - Matrícula Escolar';
            $email = $interview->email;

            // Usar el servicio específico si se proporciona
            $emailService = $provider ? $this->getEmailService($provider) : $this->emailService;
            $response = $emailService->send($email, $subject, $htmlContent, null, false, $cc, $bcc);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar correo de aceptación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envía correo de lista de espera para una entrevista
     */
    public function sendStandbyEmail(int $interviewId, ?string $provider = null)
    {
        try {
            $interview = CatchmentInterview::findOrFail($interviewId);

            if (!$interview->status_standby) {
                return response()->json([
                    'success' => false,
                    'message' => 'La entrevista no está en estado de espera'
                ], 400);
            }

            $institucion = Institucion::orderBy('created_at', 'DESC')->first();
            $autoridad = Autoridad::getTipoAuthority('1'); // DIRECTOR GENERAL
            $director = Autoridad::getTipoAuthority('2'); // DIRECTOR GENERAL
            $toDate = Date::now()->format('d F Y');
            $list_comment = Catchment::COLUMN_COMMENTS;
            $htmlContent = view('email.catchment.status_standby', [
                'interview' => $interview,
                'institucion' => $institucion,
                'autoridad' => $autoridad,
                'director' => $director,
                'list_comment' => $list_comment,
                'toDate' => $toDate,
            ])->render();

            $cc = $this->cc_mail;
            $bcc = $this->bcc_mail;
            $subject = 'Lista de Espera - Matrícula Escolar';
            $email = $interview->email;

            // Usar el servicio específico si se proporciona
            $emailService = $provider ? $this->getEmailService($provider) : $this->emailService;
            $response = $emailService->send($email, $subject, $htmlContent, null, false, $cc, $bcc);

            // $sendPulseService = app(SendPulseService::class);
            // $response = $sendPulseService->send($email, $subject, $htmlContent, null, false, $cc, $bcc);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar correo de lista de espera: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un registro de catchment
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    // public function destroy($id, Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $catchment = Catchment::findOrFail($id);

    //         // Verificar si hay entrevistas asociadas
    //         $hasInterviews = CatchmentInterview::where('catchment_id', $id)->exists();
    //         if ($hasInterviews) {
    //             throw new \Exception('No se puede eliminar el registro porque tiene entrevistas asociadas.');
    //         }

    //         $catchment->delete();
    //         DB::commit();

    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => trans('db_oper_result.delete_ok')
    //             ]);
    //         }

    //         Session::flash('operp_ok', trans('db_oper_result.delete_ok'));
    //         return redirect()->route('bienestars.matriculations.catchments.index');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Error al eliminar el registro ' . $e->getMessage()
    //             ], 500);
    //         }
    //         Session::flash('operp_error', 'Error al eliminar el registro ' . $e->getMessage());
    //         return redirect()->back();
    //     }
    // }

    public function destroy($id, Request $request)
    {
        $catchment = Catchment::findOrFail($id);
        $messenge = trans('db_oper_result.destroy_not_ok');
        if ($catchment->status_delete) {
            // $catchment->delete();
            $catchment->status_active = false;
            $catchment->save();
            $messenge = trans('db_oper_result.delete_ok');
            $operation = 'delete';
            if ($request->ajax()) {
                return response()->json([
                    "messenge" => $messenge,
                    "operation" => $operation,
                ]);
            }
        }
        Session::flash('operp_ok', $messenge);
        return redirect()->route('bienestars.matriculations.catchments.index');
    }

    public function force_destroy($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $catchment = Catchment::findOrFail($id);

            // Eliminar en cascada las entrevistas asociadas
            foreach ($catchment->catchmentInterviews as $interview) {
                $interview->delete();
            }

            $catchment->delete();
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'El registro ha sido eliminado completamente de la base de datos'
                ]);
            }

            Session::flash('operp_ok', 'El registro ha sido eliminado completamente de la base de datos');
            return redirect()->route('bienestars.matriculations.catchments.index');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar definitivamente: ' . $e->getMessage()
                ], 500);
            }
            Session::flash('operp_error', 'Error al eliminar definitivamente: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Elimina un registro de entrevista
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy_interview($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $interview = CatchmentInterview::findOrFail($id);

            // Verificar si la entrevista está aceptada
            if ($interview->accepted) {
                throw new \Exception('No se puede eliminar una entrevista que ya ha sido aceptada.');
            }

            $interview->delete();
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('db_oper_result.delete_ok')
                ]);
            }

            Session::flash('operp_ok', trans('db_oper_result.delete_ok'));
            return redirect()->route('bienestars.matriculations.interviews.index');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la entrevista: ' . $e->getMessage()
                ], 500);
            }
            Session::flash('operp_error', 'Error al eliminar la entrevista: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
