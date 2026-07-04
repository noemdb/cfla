<?php

namespace App\Models\app\SocialAction;

use App\Models\app\Estudiant;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'estudiant_id',
        'community_action_id',
        'date',
        'duration',
        'observations',
    ];

    const COLUMN_COMMENTS = [
        'user_id' => 'Usuario',
        'estudiant_id' => 'Estudiante',
        'community_action_id' => 'Actividad de acción comunitaria',
        'date' => 'Fecha en la que se realizó la actividad',
        'duration' => 'Cantidad de horas realizadas',
        'observations' => 'Observaciones/Incidencia',
        'status' => 'Estado de aprobación',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function community_action()
    {
        return $this->belongsTo(CommunityAction::class);
    }
    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class);
    }

    public function getStatusNameAttribute()
    {
        $status = [
            'pending' => 'Pendiente',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
        ];
        return $status[$this->status];
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function getStatusDeleteAttribute()
    {
        $now = Carbon::now()->format('Y-m-d');
        $date = ($this->community_action) ? $this->community_action->date : null ;
        return ( $now < $date) ? true : false ;
    }

    public function getStatusEditAttribute()
    {
        $now = Carbon::now()->format('Y-m-d');
        $date = ($this->community_action) ? $this->community_action->date : null ;
        return ( $now <= $date) ? true : false ;
    }
}
