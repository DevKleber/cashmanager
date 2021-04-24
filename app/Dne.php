<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dne extends Model
{
    protected $table = 'pessoa.localidade';
    protected $primaryKey = 'id_localidade';
    protected $fillable = ['id_localidade', 'id_estado', 'id_localidade_pai', 'no_localidade', 'bo_municipio', 'created_at', 'updated_at'];
}
