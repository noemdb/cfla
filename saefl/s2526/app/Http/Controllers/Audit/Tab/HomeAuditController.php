<?php

namespace App\Http\Controllers\Audit\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\Lapso;
use Illuminate\Http\Request;

class HomeAuditController extends Controller
{
    use UserDataInitializer;

    public function __construct()
    {
        $this->middleware(['auth', 'is_audit', function ($request, $next) {
            $this->initializeUserData();
            return $next($request);
        }]);
    }

    public function home()
    {
        $user=$this->user; //dd($user);
        $autoridad=$this->autoridad;
        $list_comment_autoridad=$this->listCommentAutoridad; //dd($this);

        return view('audits.home',compact('user','autoridad','list_comment_autoridad'));
    }
}
