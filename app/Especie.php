<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Especie extends Model
{
    protected $table = 'rebanho.especie';
    protected $primaryKey = 'id_especie';
    protected $fillable = ['id_especie', 'id_arquivo', 'no_especie', 'bo_ativo'];

    public static function getEspecieByNucleo($id_nucleo)
    {
        return \App\Nucleo::where('id_nucleo', $id_nucleo)
        ->Join('rebanho.especie as e', 'e.id_especie', '=', 'nucleo.id_especie')
        ->select('e.*')
        ->first()
    ;
    }
}
