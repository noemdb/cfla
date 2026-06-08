<?php

namespace App\Models\app\Common;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = [
        'name','type','view','module','weighing','status','description'
    ];

    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'name' => 'Nombre',
        'type' => 'Tipo',
        'view' => 'Vista',
        'module' => 'Módulo',
        'weighing' => 'Ponderación',
        'status' => 'Estado',
        'description' => 'Descripción',
    ];

    public static function list_status() /* usada para llenar los objetos de formularios select*/
    {
        return Status::select('id', 'name')->orderby('name','asc')->pluck('name', 'id');
    }
}

/*
$table->smallIncrements('id');
$table->string('name')->comment('Nombre');
$table->string('type')->nullable()->comment('Tipo');
$table->string('view')->nullable()->comment('Vista');
$table->string('module')->nullable()->comment('Módulo');
$table->float('weighing',3,2)->default(1.00)->comment('Ponderación');
$table->enum('status',['true','false'])->default('true')->comment('Estado');
$table->string('description')->nullable()->comment('Descripción');
$table->timestamps();
*/
