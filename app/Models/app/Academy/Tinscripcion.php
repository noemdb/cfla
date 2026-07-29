<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tinscripcion extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $table = 'tinscripcions';
}
