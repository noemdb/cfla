<?php

namespace App\Http\Controllers\Admin\Database;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FixDebateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_admon']);
    }

    public function clone(Request $request)
    {
        dd($request->all());
    }
}
