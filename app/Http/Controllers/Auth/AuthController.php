<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Ghi\IntranetAuth\AuthenticatesIntranetUsers;


class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesIntranetUsers, ThrottlesLogins;
    
    protected $redirectPath = 'proyectos';


    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'usuario' => 'required', 'clave' => 'required',
        ]);

        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $request->only('usuario', 'clave');

        $user = DB::connection('sca')->select(DB::raw("select * from `igh`.`usuario` where `usuario` = '".$credentials["usuario"]."' and crc32(`clave` = '".$credentials["clave"]."')"));

        if(auth()->attempt(['usuario' => $request->input('usuario'), 'clave' => $request->input('clave')])){
            //dd("dentro");

            $new_session_id = \Session::getId();

            if($user[0]->session_id != ''){

                $last_session = \Session::getHandler()->read($user[0]->session_id);

                if($last_session){
                    if(\Session::getHandler()->destroy($user[0]->session_id)){

                    }
                }
            }

            \DB::table('igh.usuario')->where('idusuario', $user[0]->idusuario)->update(['session_id' => $new_session_id]);

            $user = auth()->user();
        }

        if (auth()->attempt($credentials, $request->has('remember_me'))) {
            if ($throttles) {
                $this->clearLoginAttempts($request);
            }

            return redirect($this->redirectPath());
        }

        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return redirect($this->loginPath())
            ->withInput($request->only('usuario', 'remember_me'))
            ->withErrors([
                'usuario' => $this->getFailedLoginMessage(),
            ]);
    }

}
