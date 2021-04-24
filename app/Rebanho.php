<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rebanho extends Model
{
    protected $table = 'rebanho.rebanho';
    protected $primaryKey = 'id_rebanho';
    protected $fillable = ['id_nucleo', 'id_raca', 'created_at', 'updated_at', 'nu_identificacao', 'no_identificacao', 'bo_vivo', 'dt_nascimento', 'bo_macho', 'bo_fiscal'];

    public static function getRebanhoByNucleo($id_nucleo, $fiscal)
    {
        return \App\Rebanho::where('id_nucleo', $id_nucleo)
            ->join('rebanho.raca as r', 'r.id_raca', '=', 'rebanho.id_raca')
            ->where('bo_vivo', true)
            ->where('bo_fiscal', $fiscal)
            ->selectRaw(
                " *,(DATE_PART('month', AGE(now(), dt_nascimento))) as mes, DATE_PART('year', AGE(now(), dt_nascimento)) as ano,	CONCAT(no_identificacao, ' - ', nu_identificacao) AS nome_codigo"
            )
            ->get()
    ;
    }

    public static function getRebanhoComIdentificacaoByNucleo(int $id_nucleo, bool $identificacao)
    {
        $query = \App\Rebanho::where('id_nucleo', $id_nucleo)
            ->join('rebanho.raca as r', 'r.id_raca', '=', 'rebanho.id_raca')
            ->selectRaw(
                " *,(DATE_PART('month', AGE(now(), dt_nascimento))) as mes, DATE_PART('year', AGE(now(), dt_nascimento)) as ano,	CONCAT(no_identificacao, ' - ', nu_identificacao) AS nome_codigo"
            )
        ;
        $query->where('bo_vivo', true);
        if ($identificacao) {
            $query->where(function ($query) {
                $query->whereNotNull('nu_identificacao')
                    ->orWhereNotNull('no_identificacao')
                ;
            });
        } else {
            $query->where(function ($query) {
                $query->whereNull('nu_identificacao')
                    ->WhereNull('no_identificacao')
                ;
            });
        }

        return $query->get();
    }
}
