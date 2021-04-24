<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropriedadeGaleria extends Model
{
    protected $table = 'propriedade.galeria';
    protected $primaryKey = 'id_galeria';
    protected $fillable = ['id_galeria', 'id_arquivo', 'id_propriedade', 'bo_ativo'];
}
