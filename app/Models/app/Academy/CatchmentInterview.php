<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatchmentInterview extends Model
{
    use HasFactory;

    public static function testimonials($limit=3)
    {
        return CatchmentInterview::query()
        ->whereRaw('LENGTH(reason_for_choosing_institution) > 100')
        ->orderByRaw('RAND()')
        ->limit($limit)
        ->get();
    }
}
