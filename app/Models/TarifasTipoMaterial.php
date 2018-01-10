<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifasTipoMaterial extends Model
{
    //
    protected $connection = 'sca';
    protected $table = 'tarifas_tipo_material';
    protected $primaryKey = 'idtarifas_tipo';
    protected $fillable = ['nombre'];
    protected $presenter = ModelPresenter::class;

    public function nombre($id)
    {
      return TarifasTipoMaterial::where('idtarifas_tipo','=', $id);
    }

}


