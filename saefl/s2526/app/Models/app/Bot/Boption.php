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
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'bmain_id' => 'Ident. del Bot',
        'key' => 'Clave',
        'text' => 'Autorrespuesta',
        'description' => 'Descripción de la opción',
        'created_at' => 'Fecha'
    ];

    public function bmain()
    {
        return $this->belongsTo('App\Models\app\Bot\Bmain');
    }

    public static function getforArea($area,$is_admin=false)
    {
        $boptions = Boption::select('boptions.*')->join('bmains', 'bmains.id', '=', 'boptions.bmain_id');
        $boptions = ($is_admin) ? $boptions : $boptions->where('bmains.area',$area);
        $boptions = $boptions->get();
        return $boptions;
    }

    public function getTextHtmlAttribute()
    {
        $text = $this->text;

        //bold
        $pattern = '/\*(?=\w)/';
        $text = preg_replace($pattern, '<strong>', $text);
        $pattern = '/(?<=\w)\*/';
        $text = preg_replace($pattern, '</strong>', $text);

        //italic
        $pattern = '/\_(?=\w)/';
        $text = preg_replace($pattern, '<em>', $text);
        $pattern = '/(?<=\w)\_/';
        $text = preg_replace($pattern, '</em>', $text);

        //strike
        $pattern = '/\~(?=\w)/';
        $text = preg_replace($pattern, '<em>', $text);
        $pattern = '/(?<=\w)\~/';
        $text = preg_replace($pattern, '</em>', $text);

        //mono
        $pattern = '/\```(?=\w)/';
        $text = preg_replace($pattern, '<em>', $text);
        $pattern = '/(?<=\w)\```/';
        $text = preg_replace($pattern, '</em>', $text);

        return $text;
    }
}


/*



$table->id();
$table->smallInteger('bmain_id')->unsigned()->comment('Ident. del Bot');
$table->string('key',32)->comment('Clave');
$table->text('text')->comment('Descripción de la opción');
$table->timestamps();
$table->foreign('bmain_id')->references('id')->on('bmains')->onDelete('cascade')->onUpdate('cascade');

*/
