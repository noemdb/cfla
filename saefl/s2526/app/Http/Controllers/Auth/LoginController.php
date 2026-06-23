<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\app\Institucion;
use App\Models\app\Pescolar;
use App\Models\app\Planpago\ExchangeRate;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

// use Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/saefl/home';
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        if (!empty($request->username)) {
            $institucion = Institucion::OrderBy('created_at','DESC')->first();
            $pescolar = Pescolar::OrderBy('created_at','DESC')->first();
            // $exchange_rate = ExchangeRate::whereDate('date',Carbon::now())->first() ;

            session(['institucion_id' => $institucion->id]);
            session(['institucion_name' => $institucion->name]);
            session(['pescolar_id' => $pescolar->id]);
            session(['pescolar_name' => $pescolar->name]);
            session(['pescolar_finicial' => $pescolar->finicial]);
            session(['pescolar_ffinal' => $pescolar->ffinal]);
            session(['pescolar_color' => $pescolar->color]);
            // session(['exchange_rate' => $exchange_rate]);
        }

        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }


    /**
     * Redirect the user to the google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        // $user = Socialite::driver('google')->user();

        $social_user = Socialite::driver('google')->user();
        if ($user = User::where('email', $social_user->email)->first()) {
            return $this->authAndRedirect($user); // Login y redirección
        }
        else {
            Session::flash('noLogin','El correo electrónico ingresado no está asociado '.env('APP_NAME'));
            return redirect()->to('/login');
        }
    }

    public function authAndRedirect($user)
    {
        Auth::login($user);
        return redirect()->to('/home#');
    }

}
