<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoTipo extends Model
{
    protected $table = 'rebanho.movimentacao_tipo';
    protected $primaryKey = 'id_movimentacaotipo';
    protected $fillable = ['id_movimentacaotipo', 'no_movimentacaotipo', 'bo_entrada', 'bo_ativo', 'created_at', 'updated_at'];

    public static function getConstants()
    {
        return \Config::get('constants.tipoMovimentacoes');
    }
}
