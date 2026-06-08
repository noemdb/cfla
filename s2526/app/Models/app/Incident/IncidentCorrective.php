<?php

namespace App\Models\app\Incident;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentCorrective extends Model
{
    use HasFactory;

    protected $fillable = [
        'fault_id',
        'description',
        'status_active',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'fault_id' => 'integer',
        'status_active' => 'boolean',
    ];

    public function incident_fault() { return $this->belongsTo(IncidentFault::class, 'fault_id'); }
    public function incident_actions() { return $this->hasMany(IncidentAction::class,'corrective_id'); }

    public static function list_corrective($fault_id=null)
    {
        $list_corrective = IncidentCorrective::all()->where('status_active',true);
        $list_corrective = ($fault_id) ? $list_corrective->where('fault_id',$fault_id) : $list_corrective ;
        $list_corrective = $list_corrective->pluck('description','id');
        return $list_corrective;
    }

    
}
