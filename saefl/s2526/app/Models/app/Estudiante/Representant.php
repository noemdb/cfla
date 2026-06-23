<?php

namespace App\Models\app\Estudiante;

use App\Models\app\Incident\Incident;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Carbon;
// use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\app\Estudiante\Functions\Representants\Relations;
use App\Models\app\Estudiante\Functions\Representants\Scope;
use App\Models\app\Estudiante\Functions\Representants\Lists;
use App\Models\app\Estudiante\Functions\Representants\Administrativo;
use App\Models\app\Estudiante\Functions\Representants\Academicos;
use App\Models\app\Estudiante\Functions\Representants\FixDBs;
use App\Models\app\Estudiante\Functions\Representants\Auditoria;
use App\Models\app\Estudiante\Functions\Representants\Exchanges;

use App\User;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\Functions\Representants\AuditoriaDate;
use App\Models\app\Estudiante\Functions\Representants\Payment as RepresentantsPayment;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Planpago\AbonoAplicado;
use App\Models\app\Planpago\Payment;
use App\Models\app\Planpago\Prepago;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Estudiante\Functions\Representants\AutoPayment;
use App\Models\app\Poll\PollAnswer;
use App\Models\app\Poll\PollMain;
use stdClass;

class Representant extends Model
{

    use Relations;
    use Scope;
    use Lists;
    use Administrativo;
    use Academicos;
    use FixDBs;
    //use Auditoria;
    use AuditoriaDate;
    use Exchanges;
    use AutoPayment;

    use SoftDeletes;
    protected $fillable = ['user_id', 'ci_representant', 'name', 'phone', 'phone_old', 'cellphone', 'pmovilphone', 'whatsapp', 'status_whatsapp_verify', 'email', 'gsemail', 'status_active', 'status_blacklist', 'status_adviders'];

    const COLUMN_COMMENTS = ['user_id' => 'Nombre de usuario', 'name' => 'Nombres', 'ci_representant' => 'Cédula de Identidad', 'phone' => 'Número de teléfono fijo', 'phone_old' => 'N. de teléfono antíguo', 'pmovilphone' => 'Telef. Pago Móvil', 'cellphone' => 'Número de teléfono celular', 'whatsapp' => 'N.WhatsApp', 'status_whatsapp_verify' => 'Cta WhatsApp Verificada', 'email' => 'Correo electrónico', 'status_active' => 'Estado', 'status_blacklist' => 'Lista negra', 'status_sender' => 'Envío realizado', 'status_adviders' => 'Delegado'];

    /**
     * Obtiene todos los ingresos del representante incluyendo los eliminados (trashed)
     *
     * @param string|null $dateFrom Fecha desde (Y-m-d)
     * @param string|null $dateTo Fecha hasta (Y-m-d)
     * @param int $limit Límite de registros (default: null = sin límite)
     * @param string $orderBy Campo para ordenar (default: 'date_transaction')
     * @param string $orderDirection Dirección del orden (default: 'desc')
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getIngresosWithTrashedAttribute()
    {
        return $this->getIngresosWithTrashed();
    }

    /**
     * Método principal para obtener ingresos con eliminados
     *
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @param int|null $limit
     * @param string $orderBy
     * @param string $orderDirection
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getIngresosWithTrashed(
        $dateFrom = null,
        $dateTo = null,
        $limit = null,
        $orderBy = 'date_transaction',
        $orderDirection = 'desc',
        $relations = ['metodo_pago', 'banco', 'estudiant', 'registro_pago', 'pago', 'abono', 'creditoafavor'],
        $paginate = false,
        $perPage = 15
    ) {
        $query = Ingreso::withTrashed()
            ->where('representant_id', $this->id)
            ->with($relations);

        // Aplicar filtros de fecha
        if ($dateFrom) {
            $query->whereDate('date_transaction', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('date_transaction', '<=', $dateTo);
        }

        // Ordenamiento
        $query->orderBy($orderBy, $orderDirection);

        // Si se especifica un ordenamiento secundario
        if ($orderBy !== 'created_at') {
            $query->orderBy('created_at', 'desc');
        }

        // Aplicar límite o paginación
        if ($paginate) {
            return $query->paginate($perPage);
        } elseif ($limit) {
            return $query->limit($limit)->get();
        }

        return $query->get();
    }

    public function getNameFullAttribute()
    {
        return $this->name;
    }

    public function getFullNameWithCiAttribute()
    {
        return $this->name . ' ' . $this->lastname . ' (' . $this->ci_representant . ')';
    }

    public function getEnablePrepagosAttribute()
    {
        return $this->prepagos->where('status_approved', 'true')->whereNull('prepagos.status_apply');
    }

    public function getContactsAttribute()
    {
        return "{$this->phone} {$this->cellphone}";
    }

    public function getUsernameAttribute()
    {
        return ($this->user) ? $this->user->username : null;
    }

    public function setCreateUserGetId($area = 'REPRESENTANTE', $rol = 'REPRESENTANTE')
    {
        if (empty($this->user_id)) {
            $arr_name = explode(" ", $this->name);
            $lastName = $arr_name[0];
            $firstName = (array_key_exists(1, $arr_name)) ? $arr_name[1] : null;
            $firstChar = ($firstName) ? $firstName[0] : Str::random(1);

            $user = replace_tilde(strtolower($firstChar . $lastName));
            do {
                $ident = rand(100, 1000);
                $username = $user . $ident;
                $q = User::where('username', $username)->first();
            } while ($q);

            $sentinela = Carbon::now()->timestamp . '@saefl.test';
            $email = ($this->email) ? $this->email : $sentinela;
            $user_email = User::where('email', $email)->first();
            if ($user_email) {
                $email = $username . '@saefl.test';
                $user_email = User::where('email', $email)->first();
                if ($user_email) {
                    $email = $sentinela;
                }
            }

            $password = bcrypt($username);

            $id = DB::table('users')->insertGetId([
                'username' => $username,
                'password' => $password,
                'email' => $email,
                'is_active' => 'enable',
                'created_at' => Carbon::now(),
                'remember_token' => Str::random(10),
            ]);
            DB::table('profiles')->insert([
                'firstname' => $lastName,
                'lastname' => $firstName,
                'url_img' => "images/avatar/user_default.png",
                'user_id' => $id,
                'created_at' => Carbon::now(),
            ]);
            DB::table('rols')->insert([
                'area' => $area,
                'rol' => $rol,
                'descripcion' => "Profesor de la institución",
                'finicial' => Carbon::now()->year . "0101",
                'ffinal' => (Carbon::now()->year + 1) . "1231",
                'user_id' => $id,
                'created_at' => Carbon::now(),
            ]);
            $update = Representant::where('id', $this->id)->update(['user_id' => $id]);
            return $id;
        }
    }

    public function getNotificableFormalyEstudiants()
    {
        return Estudiant::select('estudiants.*')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('representants.id', $this->id)
            ->where('representants.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('seccions.status_inscription_affects', 'true')
            ->where('estudiants.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('estudiants.status_notice', true)
            ->get();
    }

    public function getEstudiantAgreeAttribute()
    {
        return Estudiant::select('estudiants.*')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('representants.id', $this->id)
            ->where('estudiants.status_notice', true)
            ->first();
    }

    public function getActiveAttribute()
    {
        $estudiant = Estudiant::select('estudiants.*')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('representants.id', $this->id)
            ->first();
        return ($estudiant) ? true : false;
    }
    public function getEnableAttribute()
    {
        $estudiant = Estudiant::select('*')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->where('representants.id', $this->id)
            ->where(function ($query) {
                $query->where('inscripcions.id', '<>', null)
                    ->orWhere('administrativas.id', '<>', null);
            })
            ->first();
        return ($estudiant) ? true : false;
    }

    public function getEstudiantsFormalyAttribute()
    {
        $estudiants = Estudiant::select('estudiants.*', 'grados.id as grado_id', 'seccions.id as seccion_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->where('planpagos.status_inscription_affects', 'true')
            ->where('representants.id', $this->id)
            ->where('estudiants.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('grados.status_active', 'true')
            ->where('representants.status_active', 'true')
            ->get();
        return $estudiants;
    }

    public static function representantFormaly()
    {
        $representants = Representant::select('representants.*')
            ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')

            ->where('estudiants.status_active', 'true')
            ->where('representants.status_active', 'true')
            ->where('seccions.status_active', 'true')
            ->where('pestudios.status_active', 'true')
            ->where('planpagos.status_inscription_affects', 'true')

            ->whereNull('estudiants.deleted_at')
            ->whereNull('representants.deleted_at')
            ->whereNull('seccions.deleted_at')
            ->whereNull('pestudios.deleted_at')
            ->whereNull('planpagos.deleted_at')

            ->groupBy('representants.id')

            ->get();

        return $representants;
    }

    public function getStatusPhoneAttribute()
    {
        $count = (!empty($this->phone)) ? strlen($this->phone) : null;
        $count = (!empty($this->cellphone)) ? (strlen($this->cellphone) + $count) : $count;
        $count = (!empty($this->others_phone)) ? (strlen($this->others_phone) + $count) : $count;
        return ($count > 5) ? true : false;
    }

    public function getPmovilPhoneAttribute()
    {
        $paymennts = Payment::where('ci_representant', $this->ci_representant)->get();
        $phone = '';
        foreach ($paymennts as $paymennt) {

            if ($paymennt->phone) {
                $pos = strpos($phone, $paymennt->phone);
                $phone = ($paymennt->phone && !($pos === false)) ? $paymennt->phone . ' | ' . $phone : $phone;
            }
            if ($paymennt->phone_1) {
                $pos = strpos($phone, $paymennt->phone_1);
                $phone = ($paymennt->phone_1 && !($pos === false)) ? $paymennt->phone_1 . ' | ' . $phone : $phone;
            }
            if ($paymennt->phone_2) {
                $pos = strpos($phone, $paymennt->phone_2);
                $phone = ($paymennt->phone_2 && !($pos === false)) ? $paymennt->phone_2 . ' | ' . $phone : $phone;
            }
            if ($paymennt->phone_3) {
                $pos = strpos($phone, $paymennt->phone_3);
                $phone = ($paymennt->phone_3 && !($pos === false)) ? $paymennt->phone_3 . ' | ' . $phone : $phone;
            }
        }

        return $phone;
    }

    public function getOthersPhoneAttribute()
    {
        $estudiants = $this->estudiants;
        $phone = null;
        foreach ($estudiants as $estudiant) {
            $phone = $estudiant->phone . ' - ' . $estudiant->phone;
        }
        return $phone;
    }


    public static function representantDebRandom($formaly = true)
    {
        $representants = ($formaly) ? Representant::representantFormaly()->shuffle() : Representant::inRandomOrder()->get(); //dd($allRepresentants);
        foreach ($representants as $representant) {
            $estudiants = $representant->estudiants;
            foreach ($estudiants as $estudiant) {
                if ($estudiant->exchange_ammount_expire_bill > 0) {
                    return $representant;
                }
            }
        }
    }

    public static function formalySolvents()
    {
        $representants = collect();
        $allRepresentants = Representant::representantFormaly();
        foreach ($allRepresentants as $representant) {
            $ammount = round($representant->exchange_ammount_expire_bill, 2);
            if ($ammount <= 0) {
                $representants->push($representant);
            }
        }
        return $representants;
    }

    public static function representansDeb()
    {
        $representants = collect();
        $allRepresentants = Representant::all();
        foreach ($allRepresentants as $representant) {
            $estudiants = $representant->estudiants;
            foreach ($estudiants as $estudiant) {
                if ($estudiant->exchange_ammount_expire_bill > 0) {
                    $representants->push($representant);
                }
            }
        }
        return $representants;
    }

    public static function imployeds()
    {
        $fecha = Carbon::now();
        $representants = Representant::select('representants.*')
            ->join('profiles', 'profiles.card_number', '=', 'representants.ci_representant')
            ->join('users', 'users.id', '=', 'profiles.user_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->Where('rols.ffinal', '>=', $fecha)
            ->Where('rols.finicial', '<=', $fecha)
            ->whereIn('rols.area', ['ADMINISTRACION', 'AUTORIDAD', 'DIRECTOR', 'ADMINISTRACION', 'CONTROL ESTUDIO', 'PROFESORADO'])
            ->whereIn('rols.rol', ['DIRECTOR', 'COORDINADOR', 'PROFESOR', 'ASISTENTE'])
            ->groupby('representants.ci_representant')
            ->get();
        return $representants;
    }

    public static function grantees()
    {
        $fecha = Carbon::now();
        $representants = Representant::select('representants.*')
            ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('plan_beneficos', 'estudiants.id', '=', 'plan_beneficos.estudiant_id')
            ->Where('plan_beneficos.created_at', '<=', $fecha)
            ->Where('plan_beneficos.ffinal', '>=', $fecha)
            ->groupBy('representants.id')
            ->get();
        return $representants;
    }
    public static function adviders()
    {
        $fecha = Carbon::now();
        $representants = Representant::select('representants.*')
            ->join('users', 'users.id', '=', 'representants.user_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->Where('rols.ffinal', '>=', $fecha)
            ->Where('rols.finicial', '<=', $fecha)
            ->where('rols.group', 'collaborators')
            ->get();
        return $representants;
    }

    public static function collaborators()
    {
        $fecha = Carbon::now();
        $representants = Representant::select('representants.*')
            ->join('users', 'users.id', '=', 'representants.user_id')
            ->join('rols', 'users.id', '=', 'rols.user_id')
            ->Where('rols.ffinal', '>=', $fecha)
            ->Where('rols.finicial', '<=', $fecha)
            ->where('rols.group', 'adviders')
            ->get();
        return $representants;
    }

    public static function debts()
    {
        $representants = Representant::all(); //dd($representants);
        $debts = collect();
        foreach ($representants as $representant) {
            $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill, 2);
            if ($exchange_ammount_expire_bill > 0) {
                $debts->push($representant); //dd($exchange_ammount_expire_bill);
            }
        }
        return $debts;
    }

    public function getEmailsAttribute()
    {
        $emails = array();
        $email = strtolower(trim($this->email));
        if (validate_email($email))
            $emails[] = $email;
        // foreach ($this->estudiants as $estudiant) {
        //     $email = strtolower(trim($estudiant->email));
        //     if (!empty($email)) {
        //         if (!in_array($email,$emails)) {
        //             $emails[]=$email;
        //         }
        //     }
        // }
        return collect((object) $emails);
    }

    public function status_sender($mailer_id)
    {
        $count = DB::table('maileds')
            ->select('maileds.*')
            ->join('mailers', 'mailers.id', '=', 'maileds.mailer_id')
            ->join('representants', 'representants.id', '=', 'maileds.representant_id')
            ->where('mailers.id', $mailer_id)
            ->where('representants.id', $this->id)
            ->where('maileds.available_at', '<=', Carbon::now()->timestamp)
            ->GroupBy('representants.id')
            ->count();

        return ($count > 0) ? true : false;
    }

    public function getRegistroPagoCombinadoRefundsAttribute()
    {
        $combinados = RegistroPagoCombinado::select('registro_pago_combinados.*')
            ->join('registro_pagos', 'registro_pago_combinados.id', '=', 'registro_pagos.registro_pago_combinado_id')
            ->join('credito_a_favors', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
            ->where('registro_pago_combinados.representant_idid', $this->id)
            ->where('credito_a_favors.status_omitted', 'false')
            ->whereNull('registro_pago_combinados.deleted_at')
            ->whereNull('registro_pagos.deleted_at')
            ->get();
        return $combinados;
    }

    public function getPollAnswers($poll_main_id)
    {
        $poll_main = PollMain::findOrFail($poll_main_id);
        $poll_answers = PollAnswer::select('poll_answers.*')
            ->join('poll_tokens', 'poll_tokens.token', '=', 'poll_answers.token')
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_tokens.poll_main_id')
            ->join('representants', 'representants.id', '=', 'poll_tokens.representant_id')
            ->where('representants.id', $this->id)
            ->get();
        /* Undefined variable: id (View: /home/nuser/code/s2122/resources/views/livewire/administracion/poll/table/show.blade.php)  */

        return $poll_answers;
    }

    public function getAnnouncementsAttribute()
    {
        $announcements = Incident::select('incidents.*')
            ->join('estudiants', 'estudiants.id', '=', 'incidents.estudiant_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->where('representants.id', $this->id)
            ->where('incidents.status_announcement', true)
            ->get();

        return $announcements;
    }

    public function getEstudiantsEnrollmentsAttribute()
    {
        $estudiants = Estudiant::select('estudiants.*', 'grados.id as grado_id', 'seccions.id as seccion_id')
            ->join('prosecucions', 'estudiants.id', '=', 'prosecucions.estudiant_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('seccions', 'seccions.id', '=', 'prosecucions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')

            ->leftJoin('enrollments', 'estudiants.ci_estudiant', '=', 'enrollments.ci_estudiant')
            ->where('representants.id', $this->id)
            ->whereNull('enrollments.id')
            ->get();
        return $estudiants;
    }

    public function getEstudiantsEnrollmentsInAttribute()
    {
        $estudiants = Estudiant::select('estudiants.*', 'grados.id as grado_id', 'seccions.id as seccion_id')
            ->join('prosecucions', 'estudiants.id', '=', 'prosecucions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'prosecucions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('representants', 'representants.id', '=', 'estudiants.representant_id')
            ->join('enrollments', 'estudiants.ci_estudiant', '=', 'enrollments.ci_estudiant')
            ->where('representants.id', $this->id)
            // ->whereNull('enrollments.id')
            ->get();
        return $estudiants;
    }
    public function getCatchmentsAttribute()
    {
        return \App\Models\app\Enrollment\Catchment::where('representant_ci', $this->ci_representant)->where('status_active', true)->get();
    }
}
