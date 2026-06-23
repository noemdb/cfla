<?php

namespace App\Http\Controllers\Director\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;

class AdministracionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_director']);
    }
    public function financial()
    {
        $list_comment = Autoridad::COLUMN_COMMENTS;

        return view('directors.financials.index',compact('list_comment'));
    }
}
