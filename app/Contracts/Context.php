<?php

namespace App\Contracts;

use App\Models\Proyecto;

interface Context
{
    /**
     * Set the database name for the current context
     * 
     * @param $name
     * @return void
     */
    public function setDatabaseName($name);
    public function setDatabaseNameCadeco($name);


    /**
     * Get the database name of the current context
     * 
     * @return string
     */
    public function getDatabaseName();
    public function getDatabaseNameCadeco();

    /**
     * Set the id value filter data in the current context
     * 
     * @param $id
     * @return void
     */
    public function setId($id);
    public function setIdCadeco($id);

    /**
     * Get the tenant id value for the current context
     * 
     * @return mixed
     */
    public function getId();
    public function getIdCadeco();

    /**
     * Tells if the context is set
     * 
     * @return boolean
     */
    public function isEstablished();

    /**
     * Tells if the context is not set
     *
     * @return boolean
     */
    public function notEstablished();

    public function setProyecto(Proyecto $proyecto);
    public function getProyecto();
}
