<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'pessoa.tipo_documento';
    protected $primaryKey = 'id_tipodocumento';
    protected $fillable = ['id_tipodocumento', 'no_tipodocumento', 'created_at', 'updated_at', 'bo_ativo'];
}
