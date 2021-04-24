<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RebanhoArquivo extends Model
{
    protected $table = 'rebanho.rebanho_arquivo';
    protected $primaryKey = 'id_rebanho_arquivo';
    protected $fillable = ['id_rebanho_arquivo', 'id_rebanho', 'id_arquivo', 'bo_ativo'];


}
