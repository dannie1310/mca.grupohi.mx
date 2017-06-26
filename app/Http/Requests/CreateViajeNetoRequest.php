<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateViajeNetoRequest extends Request
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
        $rules = [];

        foreach($this->get('viajes', []) as $key => $viaje) {
            // $rules['viajes.'.$key.'.Codigo']        = 'required|max:5|unique:sca.viajesnetos,Code';
            $rules['viajes.'.$key.'.Codigo']        = 'required|max:5|unique:sca.viajesnetos,Code';
            $rules['viajes.'.$key.'.Cubicacion']    = 'required|numeric';
            $rules['viajes.'.$key.'.FechaLlegada']  = 'required|date_format:"Y-m-d"';
            $rules['viajes.'.$key.'.HoraLlegada']   = 'required|date_format:H:i';
            $rules['viajes.'.$key.'.IdCamion']      = 'required|exists:sca.camiones,IdCamion';
            $rules['viajes.'.$key.'.IdOrigen']      = 'required|exists:sca.origenes,IdOrigen';
            $rules['viajes.'.$key.'.IdTiro']        = 'required|exists:sca.tiros,IdTiro';
            $rules['viajes.'.$key.'.IdMaterial']    = 'required|exists:sca.materiales,IdMaterial';
            $rules['viajes.'.$key.'.IdMotivo']      = 'required|exists:sca.motivos_carga_manual,id';
            $rules['viajes.'.$key.'.Motivo']        = 'string|required_if:viajes.'.$key.'.IdMotivo,7';
        }

        return $rules;
    }
    
    public function messages() {
        $messages = [];
        foreach($this->get('viajes', []) as $key => $viaje) {
            $messages['viajes.'.$key.'.FechaLlegada.required'] = '(Viaje: '.$key.') El campo Fecha Llegada es obligatorio';
            $messages['viajes.'.$key.'.FechaLlegada.date_format'] = '(Viaje: '.$key.') La Fecha Llegada no corresponde al formato :format.';

            $messages['viajes.'.$key.'.HoraLlegada.required'] = '(Viaje: '.$key.') El campo Hora Llegada es obligatorio';
            $messages['viajes.'.$key.'.HoraLlegada.required'] = '(Viaje: '.$key.') La Hora Llegada no corresponde al formato :format.';

            $messages['viajes.'.$key.'.IdCamion.required'] = '(Viaje: '.$key.') El campo Camión es obligatorio';
            $messages['viajes.'.$key.'.IdCamion.exists'] = '(Viaje: '.$key.') El Camión es inválido.';

            $messages['viajes.'.$key.'.IdOrigen.required'] = '(Viaje: '.$key.') El campo Origen es obligatorio';
            $messages['viajes.'.$key.'.IdOrigen.exists'] = '(Viaje: '.$key.') El Origen es inválido.';

            $messages['viajes.'.$key.'.IdTiro.required'] = '(Viaje: '.$key.') El campo Tiro es obligatorio';
            $messages['viajes.'.$key.'.IdTiro.exists'] = '(Viaje: '.$key.') El Tiro es inválido.';

            $messages['viajes.'.$key.'.IdMaterial.required'] = '(Viaje: '.$key.') El campo Material es obligatorio';
            $messages['viajes.'.$key.'.IdMaterial.exists'] = '(Viaje: '.$key.') El Material es inválido.';

            $messages['viajes.'.$key.'.Motivo.required_if'] = '(Viaje: '.$key.') Por favor especifique un motivo.';
            $messages['viajes.'.$key.'.Motivo.string'] = '(Viaje: '.$key.') El campo Motivo debe ser una cadena de caracteres.';

            $messages['viajes.'.$key.'.Cubicacion.required'] = '(Viaje: '.$key.') El campo Cubicación es obligatorio.';
            $messages['viajes.'.$key.'.Cubicacion.numeric'] = '(Viaje: '.$key.') El campo Cubicación debe ser numérico.';

            $messages['viajes.'.$key.'.Codigo.required'] = '(Viaje: '.$key.') El campo Codigo es obligatorio.';
            $messages['viajes.'.$key.'.Codigo.unique'] = '(Viaje: '.$key.') Ya existe un Viaje Neto con el Código proporcionado.';
            $messages['viajes.'.$key.'.Codigo.max'] = '(Viaje: '.$key.') La longitud del Código debe ser máximo de 5 caracteres.';

            $messages['viajes.'.$key.'.IdMotivo.required'] = '(Viaje: '.$key.') Por favor seleccione un Motivo.';

        }
        return $messages;
    }
}
