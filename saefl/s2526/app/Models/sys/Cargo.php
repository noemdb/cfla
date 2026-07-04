<?php

namespace App\Models\sys;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    public static function list_cargos() /* usada para llenar los objetos de formularios select*/
    {
        return Cargo::where('status','true')->pluck('name','id');
    }
}
