<?php

namespace App\Models\app\Bot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boption extends Model
{
    use HasFactory;

    protected $fillable = [
        'bmain_id','key','text','description'
    ];

    const COLUMN_COMMENTS = [
        'bmain_id' => 'Ident. del Bot',
        'key' => 'Clave',
        'text' => 'Autorrespuesta',
        'description' => 'Descripción de la opción',
        'created_at' => 'Fecha'
    ];

    public function bmain()
    {
        return $this->belongsTo(Bmain::class);
    }

    public static function getForArea($area, $is_admin = false)
    {
        $boptions = Boption::select('boptions.*')
            ->join('bmains', 'bmains.id', '=', 'boptions.bmain_id');
        $boptions = ($is_admin) ? $boptions : $boptions->where('bmains.area', $area);
        $boptions = $boptions->get();
        return $boptions;
    }

    public function getTextHtmlAttribute()
    {
        $text = $this->text;

        // bold
        $text = preg_replace('/\*(?=\w)/', '<strong>', $text);
        $text = preg_replace('/(?<=\w)\*/', '</strong>', $text);

        // italic
        $text = preg_replace('/\_(?=\w)/', '<em>', $text);
        $text = preg_replace('/(?<=\w)\_/', '</em>', $text);

        // code blocks (triple backtick)
        $text = preg_replace('/\`{3}(?=\w)/', '<code>', $text);
        $text = preg_replace('/(?<=\w)\`{3}/', '</code>', $text);

        return $text;
    }
}
