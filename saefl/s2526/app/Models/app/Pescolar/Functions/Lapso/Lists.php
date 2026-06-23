<?php
namespace App\Models\app\Pescolar\Functions\Lapso;

use App\Models\app\Pescolar\Lapso;
use Illuminate\Support\Facades\DB;

trait Lists {

    public static function list_lapso()
    {
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return $list_lapso;
    }

    public static function list_lapso_final()
    {
        $lapso_ult = Lapso::orderBy('id','desc')->first();
        $lapso_final_id = $lapso_ult->id + 1;
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id')->put($lapso_final_id,'FINAL');

        return $list_lapso;
    }

}
