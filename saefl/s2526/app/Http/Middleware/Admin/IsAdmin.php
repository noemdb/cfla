<?php

namespace App\Http\Middleware\Admin;

use Closure;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Session;

class IsAdmin
{

    public function __construct(Guard $auth){

        $this->auth = $auth;

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // dd($this->auth->user());
        if(!$this->auth->user()->isAdmin()){

            Session::flash('operp_ok',trans('db_oper_result.you_not_are_admin'));

            return redirect('/home');
        }
        return $next($request);
    }
}
