<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class DestinoPresenter   extends Presenter
{
    /**
     * Regresa el estatus de la marca
     *
     * @return string
     */
    public function estatus()
    {
        return $this->entity->Estatus == 1 ? 'ACTIVO' : 'INACTIVO';
    }
    
    public function clave() {
        return $this->entity->Clave . $this->entity->IdTiro;
    }
}