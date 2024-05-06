<?php

namespace App\Models\app\Academy\Interrogation;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'status',
    ];

    const COLUMN_COMMENTS = [
        'user_id' => 'Identificador del usuario',
        'name' => 'Nombre de la entrevista',
        'description' => 'Descripción',
        'status' => 'Estado',
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id');}

    public function interview_questions() { return $this->hasMany(InterviewQuestion::class,'interview_id'); }

    

}
