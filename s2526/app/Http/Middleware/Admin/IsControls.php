<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class IsControls
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
        //dd($this->auth->user());
        if(!$this->auth->user()->IsControls()){

            Session::flash('operp_ok',trans('db_oper_result.you_not_are_admin'));

            return redirect('/home');
        }
        return $next($request);
    }
}
