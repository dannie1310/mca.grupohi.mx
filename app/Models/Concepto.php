<?php

namespace App\Models;


use App\Facades\Context;

class Concepto extends \Ghi\Core\Models\Concepto
{


    /**
     * @return bool|string
     */
    public function getNivelPadreAttribute() {
        return substr($this->nivel, 0, strlen($this->nivel) - 4);
    }

    /**
     * @return integer
     */
    public function getIdPadreAttribute() {
        if($this->nivel_padre != '') {
            return Concepto::where('nivel', '=', $this->nivel_padre)->first()->id_concepto;
        }
        return null;
    }

    /**
     * @return string
     */
    public function getNivelHijosAttribute() {
        return $this->nivel.'___.';
    }

    public function getPathAttribute()
    {
        if($this->nivel_padre == '') {
            return $this->descripcion;
        } else {
            return Concepto::find($this->id_padre)->path . ' -> ' . $this->descripcion;
        }
    }

    /**
     * Indica si el concepto es medible
     *
     * @return bool
     */
    public function esMedible()
    {
        if ($this->concepto_medible == 3 || $this->concepto_medible == 1) {
            return true;
        }
        return false;
    }

    public function __toString()
    {
        return $this->descripcion;
    }

    public static function getNivelesRaiz()
    {
        return static::where('id_obra', Context::getIdCadeco())
            ->whereRaw('LEN(nivel) = 4')
            ->orderBy('nivel')
            ->get();
    }

    public function getHijos() {
        return  static::where('id_obra', '=', Context::getIdCadeco())
            ->where('nivel', 'like', $this->nivel_hijos)
            ->get();
    }

    public static function search($search) {
        return static::where('id_obra', Context::getIdCadeco())
            ->where('descripcion', 'LIKE', '%' . $search . '%')
            ->orWhere('clave_concepto', 'LIKE', '%' . $search . '%')
            ->limit(5)
            ->get();
    }
}