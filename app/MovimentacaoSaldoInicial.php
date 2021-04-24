<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoSaldoInicial extends Model
{
    protected $table = 'rebanho.movimentacao';
    protected $primaryKey = 'id_movimentacao';
    protected $fillable = ['id_movimentacao', 'dt_movimentacao', 'id_movimentacaotipo', 'id_pessoa', 'bo_ativo', 'bo_fiscal', 'ds_observacao'];

    public static function saveSaldoInicial($request)
    {
        if (\App\Movimentacao::checkIfNameCoreAlreadyExistsBySpecie($request['id_especie'], $request['id_propriedade'],$request['ds_nucleo'])) {
            return response(['response' => 'Já existe um núcleo com o nome ('.$request['ds_nucleo'].') para essa espécie.'], 400);
        }

        $estratificacoes = \App\Estratificacao::getAllStratificationToFilter();
        \DB::beginTransaction();
        $nucleo = \App\Nucleo::create($request);
        if (!$nucleo) {
            \DB::rollBack();

            return response(['response' => 'Erro ao criar o núcleo.'], 400);
        }
        $request['id_nucleo'] = $nucleo->id_nucleo;
        $request['id_pessoa'] = auth('api')->user()->id_pessoa;
        $movimentacao = \App\Movimentacao::create($request);
        if (!$movimentacao) {
            \DB::rollBack();

            return response(['response' => 'Erro ao criar a movimentação.'], 400);
        }

        foreach ($request['estratificacao'] as $key => $value) {
            $rebanhoManualLancado = \App\Movimentacao::lancarRebanhoManual($nucleo, $movimentacao, $value, $estratificacoes);
            if (!$rebanhoManualLancado) {
                return response(['response' => 'Erro ao salvar o rebanho.'], 400);
            }
            $rebanhoDeclaradoLancado = \App\Movimentacao::lancarRebanhoDeclarado($nucleo, $movimentacao, $request, $value, $estratificacoes);
            if (!$rebanhoDeclaradoLancado) {
                return response(['response' => 'Erro ao salvar o rebanho.'], 400);
            }
        }
        \DB::commit();

        return response($movimentacao);
    }
}
