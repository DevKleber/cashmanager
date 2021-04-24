<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nucleo extends Model
{
    protected $table = 'rebanho.nucleo';
    protected $primaryKey = 'id_nucleo';
    protected $fillable = ['id_nucleo', 'id_especie', 'ds_nucleo', 'created_at', 'updated_at', 'id_propriedade'];

    public static function getAllNucleo($coluns = [])
    {
        $query = self::join('rebanho.especie as e', 'e.id_especie', '=', 'rebanho.nucleo.id_especie')
            ->join('propriedade.propriedade as p', 'p.id_propriedade', '=', 'rebanho.nucleo.id_propriedade')
            ->select('rebanho.nucleo.*', 'p.no_propriedade', 'e.no_especie')
        ;

        foreach ($coluns as $key => $value) {
            $query->where("{$key}", $value);
        }

        $whereJoin = ['p.no_propriedade', 'e.no_especie'];
        $query = Filter::searchWhere($query, __CLASS__, $whereJoin);
        $query = Filter::orderBy($query);

        return Filter::paginate($query);
    }
    public static function getNucleoPropriedadeByNucleo($id_nucleo)
    {
        return self::join('propriedade.propriedade as p', 'p.id_propriedade', '=', 'rebanho.nucleo.id_propriedade')
            ->where('id_nucleo', $id_nucleo)
            ->first()
        ;
    }
}
