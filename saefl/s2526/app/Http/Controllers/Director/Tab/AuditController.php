<?php

namespace App\Http\Controllers\Director\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_director']);
    }
    public function usages()
    {
        $list_comment = Autoridad::COLUMN_COMMENTS;

        return view('directors.audits.index',compact('list_comment'));
    }
}
