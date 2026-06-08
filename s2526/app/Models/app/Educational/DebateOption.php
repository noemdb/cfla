<?php

namespace App\Models\app\Educational;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebateOption extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_id',
        'user_id',
        'text',
        'observation',
        'status_option_correct',
        'status_wrong_answer',
        'attachment',
        'context',
    ];

    const COLUMN_COMMENTS = [
        'question_id' => 'Pregunta',
        'user_id' => 'Última revisión',
        'text' => 'Texto',
        'observation' => 'Observación adicional',
        'status_option_correct' => 'Opción correcta',
        'status_wrong_answer' => 'Opción erronea seleccionada',
        'attachment' => 'Archivo adjunto',
        'context' => 'Contexto',
    ];

    // Relación
    public function answers() { return $this->hasMany(DebateAnswer::class,'option_id'); }
    public function question() { return $this->belongsTo(DebateQuestion::class,'question_id'); }
    public function user() { return $this->belongsTo(User::class,'user_id'); }

    // Scope para obtener las opciones activas
    public function scopeActive($query)
    {
        return $query->whereHas('question', function ($q) {
            $q->where('status_active', true);
        });
    }
    // Scope para obtener las opciones inactivas
    public function scopeInactive($query)
    {
        return $query->whereHas('question', function ($q) {
            $q->where('status_active', false);
        });
    }

    public function getAttachmentUrlAttribute()
    {
        return ($this->attachment) ? asset('storage/educationals/'.$this->attachment) : null;
    }
    
}
