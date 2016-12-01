<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class TiroPresenter   extends Presenter
{
    /**
     * Regresa el estatus del tiro
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