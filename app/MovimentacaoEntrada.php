<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoEntrada extends Model
{
    protected $table = 'rebanho.movimentacao';
    protected $primaryKey = 'id_movimentacao';
    protected $fillable = ['id_movimentacao', 'dt_movimentacao', 'id_movimentacaotipo', 'id_pessoa', 'bo_ativo', 'bo_fiscal', 'ds_observacao'];

    public static function saveMovimentacaoEntrada($request)
    {
        \DB::beginTransaction();

        $request['id_pessoa'] = auth('api')->user()->id_pessoa;
        $movimentacao = \App\Movimentacao::create($request);
        $nucleo = \App\Nucleo::where('id_nucleo', $request['id_nucleo'])->first();

        if (!$movimentacao) {
            \DB::rollBack();

            return response(['response' => 'Erro ao criar a movimentação.'], 400);
        }
        foreach ($request['rebanho'] as $key => $value) {
            $rebanho = \App\Movimentacao::lancarRebanhoDeclarado($nucleo, $movimentacao, $request, $value);
            if (!$rebanho) {
                \DB::rollBack();

                return response(['response' => 'Erro ao inativar rebanho.'], 400);
            }
        }

        \DB::commit();

        return response($movimentacao);
    }
}
