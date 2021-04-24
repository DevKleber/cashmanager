<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusFuncionario extends Model
{
    protected $table = 'pessoa.statusfuncionario';
    protected $primaryKey = 'id_statusfuncionario';
    protected $fillable = ['id_statusfuncionario', 'ds_statusfuncionario', 'created_at', 'updated_at', 'bo_ativo'];
}
