<?php

namespace App;

use App\Models\ConfiguracionDiaria\Configuracion;
use App\Models\Telefono;
use App\Models\Proyecto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Ghi\Core\App\Auth\AuthenticatableIntranetUser;
use Laracasts\Presenter\PresentableTrait;
use App\Presenters\UserPresenter;
use Illuminate\Support\Facades\DB;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Support\Facades\Config;
use App\Facades\Context;
use Auth;


class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use AuthenticatableIntranetUser,
        Authorizable,
        CanResetPassword,
        PresentableTrait,
        EntrustUserTrait {EntrustUserTrait ::can insteadof Authorizable;}

    protected $table = 'igh.usuario';
    protected $primaryKey = 'idusuario';
    protected $fillable = ['usuario', 'nombre', 'correo', 'clave'];
    protected $hidden = ['clave', 'remember_token','session_id'];
    protected $presenter = UserPresenter::class;
    public $timestamps = false;

    public function proyectos() {
        return $this->belongsToMany(Models\Proyecto::class, 'sca_configuracion.usuarios_proyectos', 'id_usuario_intranet', 'id_proyecto')
            ->where('sca_configuracion.proyectos.nuevo_esquema', '=', '1')
            ->where('sca_configuracion.usuarios_proyectos.estatus', '=', '1');
    }
    
    public function rutas() {
        return $this->hasMany(Models\Ruta::class, 'Registra');
    }
    
    public function Scopelist_proyecto($query, $id_proyecto) {
        return $query->select(DB::raw('CONCAT(nombre, " ", apaterno, " ", amaterno) AS nombre_completo, idusuario'))
                ->join('sca_configuracion.usuarios_proyectos', 'usuario.idusuario', '=', 'sca_configuracion.usuarios_proyectos.id_usuario_intranet')
                ->where('sca_configuracion.usuarios_proyectos.id_proyecto', '=', $id_proyecto)
                ->lists('nombre_completo', 'idusuario');
    }
    
    public function origenes() {
        return $this->belongsToMany(Models\Origen::class, 'origen_x_usuario', 'idusuario_intranet', 'idorigen');
    }

    public function roles()
    {
        return $this->belongsToMany(Config::get('entrust.role'), Config::get('entrust.role_user_table'), 'user_id', 'role_id')->where('id_proyecto', Context::getId());
    }

    public function rolesApi($id_role){
        $resp = Proyecto::select('proyectos.id_proyecto', 'proyectos.base_datos', 'proyectos.descripcion')
                    ->join('role_user', 'proyectos.id_proyecto', '=', 'role_user.id_proyecto')
                    ->join('roles', 'role_user.role_id', '=', 'roles.id')
                    ->where('role_user.user_id', Auth::user()->idusuario)
                    ->where('roles.name', $id_role)->get()->toArray();

        if(count($resp) == 1 ) {
            return $resp[0];
        } else {
            return null;
        }
    }

    public function configuracion() {
        return $this->hasOne(Configuracion::class, 'id_usuario');
    }

    public function __toString()
    {
        return $this->present()->nombreCompleto;
    }

    public function detachRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }

        DB::connection('sca')->table('sca_configuracion.role_user')->where([
            'user_id' => $this->idusuario,
            'role_id' => $role,
            'id_proyecto' => Context::getId()
        ])->delete();
    }
    public function telefono() {
        return $this->hasOne(Telefono::class, 'id_checador')->where('telefonos.estatus', '=', 1);
    }
}
