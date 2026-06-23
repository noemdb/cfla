<?php

namespace App\Models\app\Incident;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentDuty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status_active',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status_active' => 'boolean',
    ];

    public function incident_faults()
    {
        return $this->hasMany(IncidentFault::class, 'duty_id', 'id');
    }

    public static function list_duty($faul_id=null)
    {
        return IncidentDuty::all()->where('status_active',true)->pluck('name','id') ;
    }

    // public static function list_duty($faul_id=null)
    // {
    //     $indent_fault = IncidentFault::find($faul_id);
    //     $list_duty = IncidentDuty::all()->where('status_active',true);
    //     $list_duty = ($indent_fault) ? $list_duty->where('id',$indent_fault->duty_id) : $list_duty ;
    //     $list_duty = $list_duty->pluck('description','id');
    //     return $list_duty;
    // }

}
