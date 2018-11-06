<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateTarifaRutaMaterialRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id_material' => 'required|numeric|exists:sca.materiales,IdMaterial',
            'id_ruta' => 'required|numeric|exists:sca.rutas,IdRuta',
            'primer_km' => 'required|numeric|min:0',
            'km_subsecuente' => 'required|numeric|min:0',
            'km_adicional' => 'numeric|min:0',
            'idtipo_tarifa' => 'required|numeric|exists:sca.tipo_tarifa,id'
        ];
    }

    public function messages()
    {
        $messages = [
            'id_material.required' =>'El campo Material es obligatorio.',
            'id_ruta.required' => 'El campo Ruta es obligatorio.',
            'primer_km.required' => 'El campo Tarifa Primer KM es obligatorio.',
            'km_subsecuente.required' => 'El campo Tarifa KM Subsecuente es obligatorio.',
            'idtipo_tarifa.required' => ' El campo Tipo de Tarifa es obligatorio.',
            'primer_km.numeric' => 'El campo Tarifa Primer KM debe ser numérico.',
            'km_subsecuente.numeric' => 'El campo Tarifa KM Subsecuente debe ser numérico.',
            'id_material.exists'   => 'No existe un Material con el Id: '. $this->id_material,
            'id_ruta.exists' => 'No existe una Ruta con el Id: '.$this->id_ruta
        ];

        return $messages;
    }
}
