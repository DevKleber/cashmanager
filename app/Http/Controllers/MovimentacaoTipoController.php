<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class MovimentacaoTipoController extends Controller
{
    public function index()
    {
        $movimentacaoTipo = \App\MovimentacaoTipo::where('bo_ativo', true)->get();
        if (!$movimentacaoTipo) {
            return response(['response' => 'Não existe movimentação tipo'], 400);
        }

        return response(['dados' => $movimentacaoTipo]);
    }

    public function store(Request $request)
    {
        $request['bo_ativo'] = true;

        $movimentacaoTipo = \App\MovimentacaoTipo::create($request->all());
        if (!$movimentacaoTipo) {
            return  response(['response' => 'Erro ao salvar movimentação tipo'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $movimentacaoTipo]);
    }

    public function show($id)
    {
        $movimentacaoTipo = \App\MovimentacaoTipo::find($id);
        if (!$movimentacaoTipo) {
            return response(['response' => 'Não existe movimentação tipo'], 400);
        }

        return response($movimentacaoTipo);
    }

    public function update(Request $request, $id)
    {
        $movimentacaoTipo = \App\MovimentacaoTipo::find($id);

        if (!$movimentacaoTipo) {
            return response(['response' => 'MovimentacaoTipo Não encontrado'], 400);
        }
        $movimentacaoTipo = Helpers::processarColunasUpdate($movimentacaoTipo, $request->all());

        if (!$movimentacaoTipo->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $movimentacaoTipo = \App\MovimentacaoTipo::find($id);

        if (!$movimentacaoTipo) {
            return response(['response' => 'MovimentacaoTipo Não encontrado'], 400);
        }
        $movimentacaoTipo->bo_ativo = false;
        if (!$movimentacaoTipo->save()) {
            return response(['response' => 'Erro ao deletar movimentação tipo'], 400);
        }

        return response(['response' => 'MovimentacaoTipo Inativado com sucesso']);
    }
}
