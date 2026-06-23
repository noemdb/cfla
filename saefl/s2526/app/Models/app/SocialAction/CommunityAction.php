<?php

namespace App\Models\app\SocialAction;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Grado;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CommunityAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'grado_id',
        'title',
        'description',
        'observations',
        'date',
        'duration',
        'status',
        'type',
        'entity_benefic',
        'location',
        'required',
        'image',
    ];

    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario',
        'grado_id' => 'Grado',
        'title' => 'Título',
        'description' => 'Descripción',
        'observations' => 'Observaciones sobre la actividad (opcional)',
        'date' => 'Fecha',
        'duration' => 'Duración en horas',
        'status' => 'Estado (activa o inactiva)',
        'type' => 'Tipo de actividad (individual o grupal)',
        'entity_benefic' => 'Entidad beneficiada por la actividad (opcional)',
        'location' => 'Lugar donde se realiza la actividad (opcional)',
        'required' => 'Requisitos para participar en la actividad (opcional)',
        'image' => 'Imagen (opcional)',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }
    public function community_hours()
    {
        return $this->hasMany(CommunityHour::class);
    }

    public static function getTypes()
    {
        return [
            'individual' => 'Individual',
            'group' => 'Grupal',
        ];
    }

    public static function getStatus()
    {
        return [
            true => 'Activa',
            false => 'Desactiva',
        ];
    }

    public function scopeActive()
    {
        return $this->where('status', true);
    }

    public function getImageUrlAttribute()
    {
        return ($this->image) ? asset('storage/social_accions/'.$this->image) : null;
    }

    public function getStatusDeleteAttribute()
    {
        $now = Carbon::now()->format('Y-m-d');
        $status = ($this->community_hours->isEmpty()) ? true : false ; //dd($this->community_hours);
        // return ( $now < $this->date && $status ) ? true : false ;
        return $status ;
    }

    public function getStatusEditAttribute()
    {
        $now = Carbon::now()->format('Y-m-d');
        // return ( $now <= $this->date) ? true : false ;
        return true ;
    }

    public function getEstudiantsAttribute()
    {
        return Estudiant::select('estudiants.*')
        ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        ->join('seccions', 'inscripcions.seccion_id', '=', 'seccions.id')
        ->join('grados', 'seccions.grado_id', '=', 'grados.id')
        ->Where('grados.id', '=', $this->grado_id)
        ->where('grados.status_active','true')
        ->where('seccions.status_active','true')
        ->where('estudiants.status_active','true')
        ->wherenull('inscripcions.deleted_at')
        ->wherenull('seccions.deleted_at')
        ->wherenull('grados.deleted_at')
        ->orderBy('estudiants.ci_estudiant','asc')
        ->get();
    }

    public function getHoursCompletedAttribute()
    {
        return CommunityHour::query()
        ->where('community_action_id',$this->id)
        // ->where('status', 'approved')
        ->sum('duration');
    }

    public static function list_community_actions()
    {
        $community_actions = CommunityAction::query()
        ->select('id')
        ->selectRaw('CONCAT("[",type,"]", " ",title ) as action_fullname')
        ->where('user_id',Auth::id())
        ->pluck('action_fullname','id');

        return $community_actions;
    }

}











