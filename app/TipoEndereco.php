<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoEndereco extends Model
{
    protected $table = 'pessoa.tipo_endereco';
    protected $primaryKey = 'id_tipoendereco';
    protected $fillable = ['id_tipoendereco', 'no_tipoendereco', 'created_at', 'updated_at', 'bo_ativo'];
}
