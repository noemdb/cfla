<?php

namespace App\Http\Controllers\General\Polls;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PollMainController extends Controller
{
    public function index($token)
    {
        return view('general.polls.index',compact('token'));
    }
}
