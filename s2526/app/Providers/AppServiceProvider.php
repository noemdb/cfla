<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

use App\Models\app\Planpago\ExchangeRate;
use App\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Services\GeminiService::class, function ($app) {
            return new \App\Services\GeminiService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register DiagReport Observer for automatic audit logging
        \App\Models\app\Instrument\DiagReport::observe(\App\Observers\DiagReportObserver::class);

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        //limittes de los strin en la base de datos
        Schema::defaultStringLength(191);

        setlocale(LC_ALL, 'es_ES');
        Carbon::setLocale('es');
        Date::setLocale('es');

        // Configuración para fechas en español
        Carbon::setLocale(config('app.locale'));
        setlocale(LC_ALL, 'es_MX', 'es', 'ES', 'es_MX.utf8');

        ini_set('max_execution_time', 10000);
        set_time_limit(10000);
        ini_set('memory_limit', '1024M');

        try {
            // DB::connection()->getPdo();
            $exchange_rate = ExchangeRate::whereDate('date', Carbon::now())->first();
        } catch (\Exception $e) {
            $exchange_rate = null;
        }
        // DB::connection()->getPdo();
        // $exchange_rate = ( DB::connection()->getDatabaseName() ) ? ExchangeRate::whereDate('date',Carbon::now())->first() : null ;
        // $exchange_rate = Session::has('exchange_rate') ;

        View::share('exchange_rate_current', $exchange_rate);

        require(__DIR__ . '/include/icons.php');

        DB::disableQueryLog();

        /*directivas personalizadas de blade (funciones contenidad en app/Support/helpers)*/

        Blade::if('admin', function () {
            $user = User::find(auth()->id());
            return ($user) ? auth()->check() && $user->isAdmin() : null;
        });

        Blade::if('admon', function () {
            $user = User::find(auth()->id());
            return ($user) ? auth()->check() && $user->isAdmon() : null;
        });

        Blade::if('control', function () {
            $user = User::find(auth()->id());
            return ($user) ? auth()->check() && $user->isControl() : null;
        });

        Blade::if('representant', function () {
            $user = User::find(auth()->id());
            return ($user) ? auth()->check() && $user->isRepresentant() : null;
        });

        Blade::if('profesor', function () {
            $user = User::find(auth()->id());
            return ($user) ? auth()->check() && $user->isProfesor() : null;
        });

        Blade::if('evaluacion', function () {
            $user = User::find(auth()->id());
            return ($user) ? auth()->check() && $user->IsEvaluacion() : null;
        });

        Blade::if('director', function () {
            $user = User::find(auth()->id());
            return ($user) ? auth()->check() && $user->IsDirector() : null;
        });

        ////////////////////////////////////////////

        // Blade::if('admon', function () {
        //     return auth()->check() && auth()->user()->isAdmon();
        // });

        // Blade::if('control', function () {
        //     return auth()->check() && auth()->user()->isControl();
        // });

        // Blade::if('representant', function () {
        //     return auth()->check() && auth()->user()->isRepresentant();
        // });

        // Blade::if('profesor', function () {
        //     return auth()->check() && auth()->user()->isProfesor();
        // });

        Blade::directive('readable_int', function ($expression) {
            return sprintf('<?php echo readable_int(%s) ; ?>', $expression);
        });

        // Usar Bootstrap para la paginación
        Paginator::useBootstrap();
    }
}
