<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoOrgaoExpedidor extends Model
{
    protected $table = 'pessoa.orgao_expedidor';
    protected $primaryKey = 'id_orgaoexpedidor';
    protected $fillable = ['id_orgaoexpedidor', 'no_orgaoexpedidor', 'created_at', 'updated_at'];
}
