<?php

namespace App\Models\app\Educational;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebateAnswer extends Model
{
    use HasFactory;
    protected $fillable = [
        'competition_id',
        'question_id',
        'option_id',
        'grado_id',
        'seccion_id',
        'group_id',
        'status_claim',
        'score',
    ];

    // Relación
    public function question() { return $this->belongsTo(DebateQuestion::class); }
    public function option() { return $this->belongsTo(DebateOption::class); }
    public function grado() { return $this->belongsTo(Grado::class); }
    public function seccion() { return $this->belongsTo(Seccion::class); }
    public function group() { return $this->belongsTo(DebateGroup::class); }

    // Accessor para obtener el texto de la opción seleccionada
    public function getOptionTextAttribute()
    {
        return $this->option->text;
    }
    // Método para marcar la respuesta como en reclamación
    public function markAsClaim()
    {
        $this->status_claim = true;
        $this->save();
    }
    // Método para marcar la respuesta como incorrecta
    public function desMarkAsClaim()
    {
        $this->status_claim = false;
        $this->save();
    }
}
