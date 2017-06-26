<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolioValeManual extends Model
{
    use \Laracasts\Presenter\PresentableTrait;
    //
    protected $connection = 'sca';
    protected $table = 'folios_vales_manuales';
    protected $primaryKey = 'id';
    protected $fillable = [
        'folio',
        'id_viaje_neto'
    ];


    protected $presenter = ModelPresenter::class;
    public $timestamps = true;

    public function viajesNetos() {
        return $this->hasOne(ViajeNeto::class, "IdViajeNeto", "id_viaje_neto");
    }

}
