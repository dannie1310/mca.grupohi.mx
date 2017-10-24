<?php
/**
 * Created by PhpStorm.
 * User: JFEsquivel
 * Date: 27/04/2017
 * Time: 05:52 PM
 */

namespace App\Models\Transformers;


use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class ConceptoTreeTransformer extends AbstractTransformer
{
    /**
     * @param Concepto|Model $concepto
     * @return array
     */
    public function transformModel(Model $concepto)
    {
        return [
            'id'       => $concepto->id_concepto,
            'nivel'    => $concepto->nivel,
            'text'     => $concepto->clave_concepto ? $concepto->clave_concepto.' - '.$concepto->descripcion : $concepto->descripcion,
            'children' => $concepto->tieneDescendientes(),
            'type'     => $concepto->activo==1?$concepto->esMaterial()&&$concepto->activo==1?'material': ($concepto->tieneDescendientes() ? 'concepto' : 'medible'):'inactivo',

        ];
    }
}