<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoArquivo extends Model
{
    protected $table = 'rebanho.movimentacao_arquivo';
    protected $primaryKey = 'id_movimentacaoarquivo';
    protected $fillable = ['id_movimentacaoarquivo', 'id_movimentacao', 'id_arquivo', 'bo_ativo', 'ds_arquivo', 'id_movimentacaotipoarquivo'];


}
