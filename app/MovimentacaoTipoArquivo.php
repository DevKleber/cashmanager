<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoTipoArquivo extends Model
{
    protected $table = 'rebanho.movimentacao_tipo_arquivo';
    protected $primaryKey = 'id_movimentacaotipoarquivo';
    protected $fillable = ['id_movimentacaotipoarquivo', 'no_tipo', 'created_at', 'updated_at', 'bo_ativo'];

    public static function getAllTypeFiles($coluns = [])
    {
        $query = self::where('bo_ativo', true);

        foreach ($coluns as $key => $value) {
            $query->where("{$key}", $value);
        }

        $whereJoin = [];
        $query = Filter::searchWhere($query, __CLASS__, $whereJoin);
        $query = Filter::orderBy($query);

        return Filter::paginate($query);
    }
}
