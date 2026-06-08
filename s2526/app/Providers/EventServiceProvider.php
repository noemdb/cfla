<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

//agregadas
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

// Models
use App\Models\sys\Logdb;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],

        //INI eventos para el registro de login y loginout
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\Admin\LogSuccessfulLogin',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\Admin\LogSuccessfulLogout',
        ],
        //FIN eventos para el registro de login y loginout
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //INI eventos de eloquent para registrar actividad de los modelos
        Event::listen('eloquent.created: *', function($model){
            $arr_class = array("Logdb","Loginout");
            $class = class_basename($model);
            if(in_array($class, $arr_class) || empty(isset(auth()->user()->id)))
                return null;
            $log = Logdb::record('created', $class);               
        });
        Event::listen('eloquent.updated: *', function($model){
            $arr_class = array("Logdb","Loginout");
            $class = class_basename($model);
            if(in_array($class, $arr_class) || empty(isset(auth()->user()->id)))
                return null;
            $log = Logdb::record('updated', $class); 
        });
        Event::listen('eloquent.deleted: *', function($model){
            $arr_class = array("Logdb","Loginout");
            $class = class_basename($model);
            if(in_array($class, $arr_class) || empty(isset(auth()->user()->id)))
                return null;
            $log = Logdb::record('deleted', $class); 
        });
        Event::listen('eloquent.restored: *', function($model){
            $arr_class = array("Logdb","Loginout");
            $class = class_basename($model);
            if(in_array($class, $arr_class) || empty(isset(auth()->user()->id)))
                return null;
            $log = Logdb::record('restored', $class); 
        });
        //FIN eventos de eloquent para registrar actividad de los modelos
    }
}
