<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagOption extends Model
{
    protected $table = 'diag_options';

    protected $fillable = [
        'question_id',
        'opcion',
        'valor',
        'orden',
    ];

    public function question()
    {
        return $this->belongsTo(DiagQuestion::class, 'question_id');
    }
}
