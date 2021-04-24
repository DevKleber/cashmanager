<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Raca extends Model
{
    protected $table = 'rebanho.raca';
    protected $primaryKey = 'id_raca';
    protected $fillable = ['id_raca', 'no_raca', 'id_especie', 'bo_ativo'];

    public static function getRacaPadrao()
    {
        return \Config::get('constants.racaPadrao');
    }
}
