<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoNascimento extends Model
{
    protected $table = 'rebanho.movimentacao';
    protected $primaryKey = 'id_movimentacao';
    protected $fillable = ['id_movimentacao', 'dt_movimentacao', 'id_movimentacaotipo', 'id_pessoa', 'bo_ativo', 'bo_fiscal', 'ds_observacao'];

    public static function saveMovimentacaoNascimento($request)
    {
        \DB::beginTransaction();

        $request['id_pessoa'] = auth('api')->user()->id_pessoa;
        $movimentacao = \App\Movimentacao::create($request);
        $nucleo = \App\Nucleo::where('id_nucleo', $request['id_nucleo'])->first();

        if (!$movimentacao) {
            \DB::rollBack();

            return response(['response' => 'Erro ao criar a movimentação.'], 400);
        }
        if (self::existeAnimaisMaior12Meses($request)) {
            \DB::rollBack();

            return response(['response' => 'O tipo nascimento não é permitido lançar animais com mais de 12 meses!'], 400);
        }

        $rebanho = \App\Movimentacao::lancarRebanhoDeclarado($nucleo, $movimentacao, $request, $request);
        if (!$rebanho) {
            \DB::rollBack();

            return response(['response' => 'Erro ao inativar rebanho.'], 400);
        }

        \DB::commit();

        return response($movimentacao);
    }

    private static function existeAnimaisMaior12Meses($request)
    {
        foreach ($request['rebanho'] as $key => $value) {
            if ($value['meses'] > 12) {
                return true;
            }
        }

        return false;
    }
}
