<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoTelefone extends Model
{
    protected $table = 'pessoa.tipo_telefone';
    protected $primaryKey = 'id_tipotelefone';
    protected $fillable = ['id_tipotelefone', 'ds_tipotelefone', 'created_at', 'updated_at', 'bo_ativo'];
}
