<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    public static function getSaldoByNucleo($id_nucleo)
    {
        $nucleo = \App\Nucleo::find($id_nucleo);
        $estratificacao = \App\Estratificacao::where('id_especie', $nucleo->id_especie)->orderBy('ordem')->get()->toArray();

        return [
            'fiscal' => self::getSaldo($estratificacao, $id_nucleo, true),
            'naoFiscal' => self::getSaldo($estratificacao, $id_nucleo, false),
        ];
    }

    public static function getSaldo($estratificacao, $id_nucleo, $bo_fiscal)
    {
        $saldoReturn = [];
        foreach ($estratificacao as $key => $value) {
            $query = \App\Rebanho::where('id_nucleo', $id_nucleo)
                ->where('bo_vivo', true)
                ->where('bo_macho', $value['bo_macho'])
                ->where('bo_fiscal', $bo_fiscal)
            ;

            if (null != $value['nu_idade_minima'] || null != $value['nu_idade_maxima']) {
                $query->whereRaw(
                    "(
                        DATE_PART('year', AGE(now(), dt_nascimento))*12) + DATE_PART('month', AGE(now(), dt_nascimento)
                    )
                        BETWEEN
                        ".$value['nu_idade_minima'].' and '.$value['nu_idade_maxima']
                );
            }
            $query->select(
                \DB::raw(" (DATE_PART('year', AGE(now(), dt_nascimento))*12) + DATE_PART('month', AGE(now(), dt_nascimento)) as mes, rebanho.rebanho.*")
            );
            $rebanho = $query;

            $saldoReturn[$key] = $value;
            $saldoReturn[$key]['saldo'] = $rebanho->count();
            $saldoReturn[$key]['rebanho'] = $rebanho->get();
        }

        return $saldoReturn;
    }

    public static function getRebanhoByNucleo($id_nucleo)
    {
        $nucleo = \App\Nucleo::find($id_nucleo);

        return [
            'nucleo' => $nucleo,
            'fiscal' => \App\Rebanho::getRebanhoByNucleo($id_nucleo, true),
            'naoFiscal' => \App\Rebanho::getRebanhoByNucleo($id_nucleo, false),
        ];
    }

    public static function getRebanhoComIdentificacaoByNucleo($id_nucleo)
    {
        $nucleo = \App\Nucleo::find($id_nucleo);

        return [
            'nucleo' => $nucleo,
            'comIdentificacao' => \App\Rebanho::getRebanhoComIdentificacaoByNucleo($id_nucleo, true),
            'semIdentificacao' => \App\Rebanho::getRebanhoComIdentificacaoByNucleo($id_nucleo, false),
        ];
    }
}
