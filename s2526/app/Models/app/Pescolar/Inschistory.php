<?php

namespace App\Models\app\Pescolar;

use App\Models\app\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inschistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'seccion_id','estudiant_id','pescolar'
    ];

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class,'estudiant_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class,'seccion_id');
    }
}
