<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoRebanho extends Model
{
    protected $table = 'rebanho.movimentacao_rebanho';
    protected $primaryKey = 'id_movimentacaorebanho';
    protected $fillable = ['id_movimentacaorebanho', 'id_rebanho', 'id_movimentacao', 'created_at', 'updated_at'];
}
