<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class MovimentacaoController extends Controller
{
    public function index($id_nucleo)
    {
        $core = \App\Nucleo::where('id_nucleo', $id_nucleo)
            ->Join('rebanho.especie as e', 'e.id_especie', '=', 'nucleo.id_especie')
            ->Join('propriedade.propriedade as p', 'p.id_propriedade', '=', 'nucleo.id_propriedade')
            ->first()
        ;

        return response(['propriedade' => $core, 'dados' => \App\Movimentacao::getMovimentsByIdCore($id_nucleo)]);
    }

    public function store(Request $request)
    {
        // return \App\MovimentacaoEntrada::saveMovimentacaoEntrada($request->all());

        $tiposDeMovimentacoes = \App\MovimentacaoTipo::getConstants();
        $tipoMovimentacao = \App\MovimentacaoTipo::where('id_movimentacaotipo', $request['id_movimentacaotipo'])->first();

        if ($request['id_movimentacaotipo'] == $tiposDeMovimentacoes['SALDO_INICIAL']) {
            unset($request['id_nucleo']);

            return \App\MovimentacaoSaldoInicial::saveSaldoInicial($request->all());
        }

        if (
            $request['id_movimentacaotipo'] == $tiposDeMovimentacoes['VENDA_GADO']
            || $request['id_movimentacaotipo'] == $tiposDeMovimentacoes['ROUBO']
            || $request['id_movimentacaotipo'] == $tiposDeMovimentacoes['MORTE']
        ) {
            try {
                return \App\MovimentacaoSaida::saveMovimentacaoSaida($request->all());
            } catch (\Throwable $th) {
                return response(['response' => $th->getMessage()], 400);
            }
        }

        if ($request['id_movimentacaotipo'] == $tiposDeMovimentacoes['NASCIMENTO']) {
            return \App\MovimentacaoNascimento::saveMovimentacaoNascimento($request->all());
        }

        if ($request['id_movimentacaotipo'] == $tiposDeMovimentacoes['CANCELAMENTO_VENDA']) {
            // precisa  mudar o banco de dados para cancelar uma venda.
            return \App\MovimentacaoCancelamentoVenda::saveMovimentacaoCancelamentoVenda($request->all());
        }
        if ($request['id_movimentacaotipo'] == $tiposDeMovimentacoes['MOVIMENTACAO_INTERNA_SAIDA']) {
            try {
                return \App\MovimentacaoInterna::saveMovimentacaoInterna($request->all());
            } catch (\Throwable $th) {
                return response(['response' => $th->getMessage()], 400);
            }
        }
    }

    public function show($id)
    {
        return response(\App\Movimentacao::detail($id));
    }

    public function update(Request $request, $id)
    {
        $movimentacao = \App\Movimentacao::find($id);

        if (!$movimentacao) {
            return response(['response' => 'Movimentacao Não encontrado'], 400);
        }
        $movimentacao = Helpers::processarColunasUpdate($movimentacao, $request->all());

        if (!$movimentacao->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $movimentacao = \App\Movimentacao::find($id);

        if (!$movimentacao) {
            return response(['response' => 'Movimentacao Não encontrado'], 400);
        }
        $movimentacao->bo_ativo = false;
        if (!$movimentacao->save()) {
            return response(['response' => 'Erro ao deletar movimentaçã'], 400);
        }

        return response(['response' => 'Movimentacao Inativado com sucesso']);
    }
}
