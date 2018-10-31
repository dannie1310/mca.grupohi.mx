<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoTarifa extends Model
{
    //
    protected $connection = 'sca';
    protected $table = 'tipo_tarifa';
    protected $primaryKey = 'id';
    protected $fillable = ['descripcion',
                            'estatus'];
    protected $presenter = ModelPresenter::class;

    public function descripcion($id)
    {
        return TipoTarifa::where('id','=', $id);
    }

}
