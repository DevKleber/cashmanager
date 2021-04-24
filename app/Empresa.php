<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'pessoa.empresa';
    protected $primaryKey = 'id_empresa';
    protected $fillable = ['id_empresa', 'ed_empresa', 'nu_cpfcnpj', 'no_razaosocial', 'no_fantasia', 'no_estado', 'no_municipio', 'ed_email', 'nu_telefone'];
}
