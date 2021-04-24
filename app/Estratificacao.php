<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estratificacao extends Model
{
    protected $table = 'rebanho.estratificacao';
    protected $primaryKey = 'id_estratificacao';
    protected $fillable = ['id_estratificacao', 'no_estratificacao', 'id_especie', 'bo_ativo', 'sigla', 'nu_idade_minima', 'nu_idade_maxima', 'bo_macho'];

    public static function getAllStratificationToFilter()
    {
        $stratification = self::where('bo_ativo', true)->get();
        $ar = [];
        foreach ($stratification as $key => $value) {
            $ar[$value['id_estratificacao']] = $value;
        }

        return $ar;
    }
}
