<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoSaida extends Model
{
    protected $table = 'rebanho.movimentacao';
    protected $primaryKey = 'id_movimentacao';
    protected $fillable = ['id_movimentacao', 'dt_movimentacao', 'id_movimentacaotipo', 'id_pessoa', 'bo_ativo', 'bo_fiscal', 'ds_observacao'];

    public static function saveMovimentacaoSaida($request)
    {
        $estratificacoes = \App\Estratificacao::getAllStratificationToFilter();
        \DB::beginTransaction();

        $request['id_pessoa'] = auth('api')->user()->id_pessoa;
        $movimentacao = \App\Movimentacao::create($request);

        if (!$movimentacao) {
            \DB::rollBack();

            return response(['response' => 'Erro ao criar a movimentação.'], 400);
        }

        foreach ($request['estratificacao'] as $key => $value) {
            \App\Movimentacao::saidaRebanhoManual($request, $movimentacao, $value, $estratificacoes);
            \App\Movimentacao::saidaRebanhoDeclarado($request['id_nucleo'], $movimentacao, $request, $value, $estratificacoes);
        }

        \DB::commit();

        return response($movimentacao);
    }
}
