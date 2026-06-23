<?php

namespace App\Models\app\Incident;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentFault extends Model
{
    use HasFactory;

    protected $fillable = [
        'description','duty_id','status_active'
    ];

    protected $dates = [
        'created_at', 'updated_at',
    ];

    protected $casts = [
        'duty_id' => 'integer',
    ];

    public function correctives()
    {
        return $this->hasMany(IncidentCorrective::class, 'fault_id', 'id');
    }

    public static function list_fault($duty_id=null)
    {
        $list_fault = IncidentFault::all()->where('status_active',true);
        $list_fault = ($duty_id) ? $list_fault->where('duty_id',$duty_id) : $list_fault ;
        $list_fault = $list_fault->pluck('description','id');
        return $list_fault;
    }

    
}
