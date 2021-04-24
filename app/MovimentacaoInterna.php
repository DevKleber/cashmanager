<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimentacaoInterna extends Model
{
    protected $table = 'rebanho.movimentacao';
    protected $primaryKey = 'id_movimentacao';
    protected $fillable = ['id_movimentacao', 'dt_movimentacao', 'id_movimentacaotipo', 'id_pessoa', 'bo_ativo', 'bo_fiscal', 'ds_observacao'];

    public static function saveMovimentacaoInterna($request)
    {
        self::validations($request['id_propriedade'], $request['id_nucleo'], $request['id_nucleo_destino']);

        $request['observacao'] = self::tratandoObservacao($request);

        \DB::beginTransaction();
        $movimentacao = self::origem($request);
        self::destino($request);

        \DB::commit();
        // \DB::rollBack();
        return $movimentacao;
    }
    private static function tratandoObservacao($request){
        $nucleoOrigem = \App\Nucleo::getNucleoPropriedadeByNucleo($request['id_nucleo']);
        $nucleoDestino = \App\Nucleo::getNucleoPropriedadeByNucleo($request['id_nucleo_destino']);
        $request['observacao'] .= "\n\nOrigem: \nPropriedade: {$nucleoOrigem->no_propriedade}\nNúcleo: {$nucleoOrigem->ds_nucleo}";
        $request['observacao'] .= "\n\nDestino: \nPropriedade: {$nucleoDestino->no_propriedade}\nNúcleo: {$nucleoDestino->ds_nucleo}";
        return $request['observacao'];
    }
    public static function origem($request)
    {
        $movimentacao = self::saveMovimentacao($request);
        self::movimentacaoRebanho($movimentacao, $request['rebanho'], $request['id_nucleo'], $request['id_nucleo_destino']);
        return $movimentacao;
    }

    public static function destino($request)
    {
        $tiposDeMovimentacoes = \App\MovimentacaoTipo::getConstants();
        $arMovimentacaoDestino = [
            "id_movimentacaotipo" => $tiposDeMovimentacoes['MOVIMENTACAO_INTERNA_ENTRADA'],
            "id_nucleo" => $request['id_nucleo_destino'],
            "bo_fiscal" => $request['bo_fiscal'],
        ];
        $movimentacaoDestino = self::saveMovimentacao($arMovimentacaoDestino);

        foreach ($request['rebanho'] as $value) {
            $movimentacaoFoiSalva = \App\Movimentacao::lancarMovimentacaoRebanho($movimentacaoDestino, $value);
            if (!$movimentacaoFoiSalva) {
                \DB::rollBack();
                throw new \Exception('Erro ao salvar rebanho');

                return false;
            }
        }

        return $movimentacaoDestino;
    }

    public static function validations($id_propriedade, $id_nucleo, $id_nucleo_destino)
    {
        if (!self::originDestinyIsOk($id_propriedade, $id_nucleo, $id_nucleo_destino)) {
            throw new \Exception('Origem e destino deve ser da mesma propriedade ');
        }

        if (!self::originHasBalance($id_nucleo)) {
            throw new \Exception('Saldo insuficiente ');
        }
    }
    public static function saveMovimentacao($request)
    {
        $request['id_pessoa'] = auth('api')->user()->id_pessoa;
        $movimentacao = \App\Movimentacao::create($request);

        if (!$movimentacao) {
            \DB::rollBack();

            throw new \Exception('Erro ao criar a movimentação.');
        }
        return $movimentacao;
    }

    private static function originHasBalance($id_nucleo)
    {
        return true;
    }

    private static function originDestinyIsOk(int $id_propriedade, int $id_nucleo, int $id_nucleo_destino): bool
    {
        $especie = \App\Especie::getEspecieByNucleo($id_nucleo);
        $total = \App\Nucleo::where('id_propriedade', $id_propriedade)
            ->where('id_especie', $especie->id_especie)
            ->whereIn('id_nucleo', [$id_nucleo, $id_nucleo_destino])
            ->count();

        if (2 != $total) {
            return false;
        }

        return true;
    }

    private static function movimentacaoRebanho($movimentacao, $rebanho, $id_nucleo, $id_nucleo_destino)
    {
        $arIdRebanho = [];
        foreach ($rebanho as $value) {
            $arIdRebanho[] = $value['id_rebanho'];

            $movimentacaoFoiSalva = \App\Movimentacao::lancarMovimentacaoRebanho($movimentacao, $value);
            if (!$movimentacaoFoiSalva) {
                \DB::rollBack();
                throw new \Exception('Erro ao salvar rebanho');

                return false;
            }
        }

        $update = \App\Rebanho::whereIn('id_rebanho', $arIdRebanho)
            ->where('id_nucleo', $id_nucleo)
            ->update(['id_nucleo' => $id_nucleo_destino]);

        if (!$update) {
            \DB::rollBack();

            throw new \Exception('Erro ao salvar a movimentação interna ');

            return false;
        }
    }
}
