<?php

namespace App\Listeners\Admin;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

//añadidos
use Illuminate\Http\Request;

//modelos
use App\Models\sys\Loginout;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        
        //INI actualiza campo(last_login_at,last_login_ip) en modelo User
        $user = $event->user;
        $user->last_login_at = date('Y-m-d H:i:s');
        $user->last_login_ip = $this->request->ip();
        $user->save();
        //FIN actualiza campo(last_login_at,last_login_ip) en modelo User
        
        $Loginout = Loginout::saveLogin($event->user->id,$event->user->username);

    }
}
