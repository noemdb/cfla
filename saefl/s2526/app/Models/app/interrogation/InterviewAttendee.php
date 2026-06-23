<?php

namespace App\Models\app\interrogation;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        
        'interview_id',
        'photo',
        'observations',
    ];

    const COLUMN_COMMENTS = [
        'user_id' => 'Identificador del usuario',
        'interview_id' => 'Identificador de la entrevista',
        'photo' => 'Foto del participante',
        'observations' => 'Observación',
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id');}
    public function interview() { return $this->belongsTo(Interview::class, 'interview_id');}

    public function getPhotoUrlAttribute()
    {
        return ($this->photo) ? asset('storage/interviews/'.$this->photo) : null;
    }
}
