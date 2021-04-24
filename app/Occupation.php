<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    protected $table = 'pessoa.cargo';
    protected $primaryKey = 'id_cargo';
    protected $fillable = ['id_cargo', 'no_cargo', 'ds_cargo', 'bo_ativo'];

    public static function getOccupation($coluns = [])
    {
        $query = \App\Occupation::query();

        if (!empty($coluns)) {
            foreach ($coluns as $key => $column) {
                $query->where($key, $column);
            }
        }
        $query = Filter::searchWhere($query, __CLASS__);
        $query = Filter::orderBy($query);

        return Filter::paginate($query);
    }
}
