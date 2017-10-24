<?php

namespace App;

use App\Models\Proyecto;
use App\Contracts\Context;
use Illuminate\Session\Store;

class ContextSession implements Context
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Set the database name for the current context
     * @param $name
     * @return void
     */
    public function setDatabaseName($name)
    {
        $this->session->put('database_name', $name);
    }

    public function setDatabaseNameCadeco($name)
    {
        $this->session->put('database_name_cadeco', $name);
    }

    /**
     * Get the database name of the current context
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->session->get('database_name');
    }

    public function getDatabaseNameCadeco()
    {
        return $this->session->get('database_name_cadeco');
    }

    /**
     * Sets the id value filter data in the current context
     * @param $id
     * @return void
     */
    public function setId($id)
    {
        $this->session->put('id', $id);
    }

    public function setIdCadeco($id)
    {
        $this->session->put('id_cadeco', $id);
    }

    /**
     * Establece el proyecto del contexto actual
     *
     * @param Proyecto $proyecto
     * @return void
     */
    public function setProyecto(Proyecto $proyecto)
    {
        $this->session->put('proyecto', $proyecto);
    }

    public function getProyecto() {
        $this->session->get('proyecto');
    }

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->session->get('id');
    }

    public function getIdCadeco()
    {
        return $this->session->get('id_cadeco');
    }

    /**
     * Tells if the context is set
     *
     * @return boolean
     */
    public function isEstablished()
    {
        return $this->getDatabaseName() && $this->getId();
    }

    /**
     * Tells if the context is not set
     *
     * @return boolean
     */
    public function notEstablished()
    {
        return ! $this->getDatabaseName() && ! $this->getId();
    }
}
