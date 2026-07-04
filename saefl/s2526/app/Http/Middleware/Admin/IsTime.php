<?php

namespace App\Http\Middleware\Admin;

use App\Models\app\Institucion;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class IsTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $now = Carbon::now();
        $institucion = Institucion::first();
        $date = ($institucion) ? $institucion->date_suspend : null ;
        if( $date >= $now ){
            Session::flash('operp_ok',trans('db_oper_result.you_not_are_admin'));
            return redirect('/home');
        }

        return $next($request);
    }
}
