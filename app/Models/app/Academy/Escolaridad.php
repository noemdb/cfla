<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escolaridad extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];
}
