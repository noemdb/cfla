<?php

namespace App\Listeners\Admin;

use Illuminate\Auth\Events\Logout;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

//añadiods
use Illuminate\Http\Request;

//modelos
use App\Models\sys\Loginout;

class LogSuccessfulLogout
{
    public $request;
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
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        //INI actualiza campo(last_login_at,last_login_ip) en modelo User
        $user = $event->user;
        if ($user) {
            $user->last_loginout_at = date('Y-m-d H:i:s');
            $user->last_login_ip = $this->request->ip();
            $user->save();
            $Loginout = Loginout::saveLoginOut($event->user->id,$event->user->username);
        }
        //FIN actualiza campo(last_login_at,last_login_ip) en modelo User
    }
}
