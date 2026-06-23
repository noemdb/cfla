<?php

namespace App\Models\app\Permission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\User;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Pevaluacion;
use Carbon\Carbon;
use Jenssegers\Date\Date;

class Pase extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','estudiant_id','profesor_id','pensum_id','type','motive','description','destination','date','time','duration','status','code_verification','require_auhtorize_guardian','require_auhtorize_teacher','require_auhtorize_manager','status_emergency','status_notifications'];

    const COLUMN_COMMENTS = [
        'user_id' => 'ID del usuario',
        'estudiant_id' => 'Estudiante',
        'profesor_id' => 'Profesor',
        'pensum_id' => 'Área de Formación',
        'type' => 'Tipo',
        'motive' => 'Motivo',
        'description' => 'Descripción',
        'destination' => 'Destino',
        'date' => 'Fecha',
        'time' => 'Hora',
        'duration' => 'Duración (Horas)',
        'status' => 'Estado',
        'code_verification' => 'Código de verificación',
        'require_auhtorize_guardian' => 'Requiere la autorización del representante',
        'require_auhtorize_teacher' => 'Requiere la autorización del profesor',
        'require_auhtorize_manager' => 'Requiere la autorización del coordinador',
        'status_emergency' => 'Es una emergencia',
        'status_notifications' => 'Notificación enviada',
    ];

    protected $casts = [
        //'date' => 'date',
    ];
    
    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCodeAttribute()
    {
        return 'PE-CO:'.str_pad($this->id, 4, "0", STR_PAD_LEFT);
    }

    public function getPestudioAttribute()
    {
        $pestudio = Pestudio::select('pestudios.*')
        ->join('grados', 'pestudios.id', '=', 'grados.pestudio_id')
        ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
        ->join('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
        ->where('inscripcions.estudiant_id',$this->estudiant_id)
        ->wherenull('grados.deleted_at')
        ->wherenull('seccions.deleted_at')
        ->wherenull('inscripcions.deleted_at')

        ->where('seccions.status_active','true')
        ->where('grados.status_active','true')

        ->first();
        return $pestudio;
    }

    public function getManagerAttribute()
    {
        $pestudio = $this->pestudio;
        return ($pestudio) ? $pestudio->manager : null ;
    }

    public function getDateTimeAttribute()
    {
        $date = $this->date;
        $time = $this->time; //dd($date,$time);
        return Date::createFromFormat('Y-m-d H:i:s',$date.' '.$time);

    }

    public static function list_type()
    {
        return [
            'Salida'=>'Salida',
            'Entrada'=>'Entrada',
            'Especial'=>'Especial',
            // 'Temporal'=>'Temporal',
            // 'Uso de Medicamentos'=>'Uso de Medicamentos',
        ];
    }

    public static function list_motive()
    {
        return [
            'Personal'=>'Personal',
            'Médico'=>'Médico',
            'Familiar'=>'Familiar',
            'Extracurricular'=>'Extracurricular',
        ];
    }

    public static function list_status()
    {
        return [
            'Pendiente'=>'Pendiente',
            'Aprobado'=>'Aprobado',
            'No aprobado'=>'No aprobado',
        ];
    }

    public static function getPasesForManagerId($manager_id)
    {
        return Pase::select('pases.*')
        ->join('pensums', 'pensums.id', '=', 'pases.pensum_id')
        ->join('grados', 'grados.id', '=', 'pensums.grado_id')
        ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
        
        ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
        ->where(
            function ($query) use ($manager_id) {
                $query->orWhere('peducativos.manager_id', $manager_id)
                    ->orWhere('peducativos.assistant_id', $manager_id)
                    ->orWhere('peducativos.deputy_id', $manager_id)
                ;
            }
        )
        
        ->where('grados.status_active','true')
        ->where('pestudios.status_active','true')

        ->wherenull('grados.deleted_at')
        ->wherenull('pensums.deleted_at')
        ->wherenull('pestudios.deleted_at')
        ->get();
    }

}

// Salida,Entrada,Especial,Temporal,Uso de Medicamentos
// Personal, Médico, Familiar, Extracurricular