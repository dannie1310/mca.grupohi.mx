<?php
/**
 * Created by PhpStorm.
 * User: EMARTINEZ
 * Date: 26/05/2017
 * Time: 12:06 PM
 */

namespace App\Http\Middleware;

use Closure;
use Laracasts\Flash\Flash;
use Zizaco\Entrust\Middleware\EntrustRole as ER;
class EntrustRole extends ER
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @param  $roles
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        if ($this->auth->guest() || !$request->user()->hasRole(explode('|', $roles))) {
            Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
            return redirect()->back();        }

        return $next($request);
    }
}
