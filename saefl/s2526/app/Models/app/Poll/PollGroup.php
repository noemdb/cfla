<?php

namespace App\Models\app\Poll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','description'
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'name' => 'Nombre del grupo',
        'description' => 'Descripción del grupo'
    ];

    public function poll_mains()
    {
        return $this->hasMany('App\Models\app\Poll\PollMain','poll_group_id');
    }

    public static function poll_group_list() /* usada para llenar los objetos de formularios select*/
    {
        return PollGroup::pluck('name','id');
    }
}

