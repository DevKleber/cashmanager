<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    protected $table = 'pessoa.endereco as e';
    protected $primaryKey = 'id_endereco';
    protected $fillable = ['id_endereco', 'id_tipoendereco', 'id_localidade', 'ds_endereco', 'ds_complemento', 'nr_cep', 'created_at', 'updated_at', 'nu_endereco'];
}
