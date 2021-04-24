<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpresaBase extends Model
{
    protected $table = 'pessoa.base';
    protected $primaryKey = 'id_base';
    protected $fillable = ['id_base', 'id_empresa', 'no_base', 'ed_base', 'bo_ativo'];
}
