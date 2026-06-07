<?php

namespace App\Models\sys;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'card_number', 'firstname', 'lastname',
        'url_img', 'dir_address',
    ];

    protected $table = 'profiles';

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
