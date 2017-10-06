<?php
/**
 * Created by PhpStorm.
 * User: DBENITEZ
 * Date: 05/10/2017
 * Time: 01:37 PM
 */

namespace App\Models\Transformers;


use App\Models\ValidacionCierrePeriodo;
use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class UsuarioCierresPeriodoTransformers extends AbstractTransformer
{

    public function transformModel(Model $usuario) {

        $output = [
            'id'=>$usuario->idusuario,
            'nombre'=>$usuario->present()->nombreCompleto,
            'cierre'=>ValidacionCierrePeriodo::usuario_cierres($usuario->idusuario)
        ];
        return $output;
    }
}