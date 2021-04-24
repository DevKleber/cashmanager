<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoEmail extends Model
{
    protected $table = 'pessoa.tipo_email';
    protected $primaryKey = 'id_tipoemail';
    protected $fillable = ['id_tipoemail', 'ds_tipoemail', 'created_at', 'updated_at', 'bo_ativo'];
}
