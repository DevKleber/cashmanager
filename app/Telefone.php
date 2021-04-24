<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Telefone extends Model
{
    protected $table = 'pessoa.telefone as t';
    protected $primaryKey = 'id_telefone';
    protected $fillable = ['id_telefone', 'nr_telefone', 'id_tipotelefone', 'created_at', 'updated_at'];
}
