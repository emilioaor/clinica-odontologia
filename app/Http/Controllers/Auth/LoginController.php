<?php

namespace App\Http\Controllers\Auth;

use App\CallBudget;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->redirectTo = route('home');
    }

    /**
     * Utilizar username para utenticacion
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        // Funcion peronalizada para validar el calendario permitido para login
        if (! $this->validateSchedule($request)) {
            $this->sessionMessage('message.user.schedule.block', self::ALERT_DANGER);

            return redirect()->route('login');
        }

        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {

            if (Auth::user()->isSellManager()) {
                $start = new \DateTime('now 00:00:00');
                $end = new \DateTime('now 23:59:59');
                $callBudgets = CallBudget::query()->whereBetween('next_call', [$start, $end])->get();
                if (count($callBudgets)) {
                    Session::put('callBudgets', $callBudgets);
                }
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Verifica si un usuario tiene permiso para iniciar sesion en
     * este horario
     *
     * @param Request $request
     * @return bool
     */
    private function validateSchedule(Request $request)
    {
        if (! $request->has($this->username())) {
            return false;
        }

        $user = User::with(['weekdays'])->where($this->username(), $request->get($this->username()))->first();

        if (! $user || ! $user->login_schedule) {
            return true;
        }

        $day = strtolower(date('l'));
        $now = new \DateTime('now');
        $sameDay = 0;

        foreach ($user->weekdays as $weekday) {
            if ($weekday->weekday === $day) {

                $sameDay++;
                $start = new \DateTime('now ' . $weekday->pivot->time_start);
                $end = new \DateTime('now ' . $weekday->pivot->time_end);

                if ($now >= $start && $now <= $end) {
                    return true;
                }
            }
        }

        if ($sameDay > 0) {
            // Esto indica que se configuro reglas de horario para este dia y no las cumplio
            return false;
        }

        return true;
    }
}
