<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class MovimentacaoRebanhoController extends Controller
{
    public function index()
    {
        $movimentacaoRebanho = \App\MovimentacaoRebanho::all();
        if (!$movimentacaoRebanho) {
            return response(['response' => 'Não existe MovimentacaoRebanho'], 400);
        }

        return response(['dados' => $movimentacaoRebanho]);
    }

    public function store(Request $request)
    {
        $movimentacaoRebanho = \App\MovimentacaoRebanho::create($request->all());
        if (!$movimentacaoRebanho) {
            return  response(['response' => 'Erro ao salvar MovimentacaoRebanho'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $movimentacaoRebanho]);
    }

    public function show($id)
    {
        $movimentacaoRebanho = \App\MovimentacaoRebanho::find($id);
        if (!$movimentacaoRebanho) {
            return response(['response' => 'Não existe MovimentacaoRebanho'], 400);
        }

        return response($movimentacaoRebanho);
    }

    public function update(Request $request, $id)
    {
        $movimentacaoRebanho = \App\MovimentacaoRebanho::find($id);

        if (!$movimentacaoRebanho) {
            return response(['response' => 'MovimentacaoRebanho Não encontrado'], 400);
        }
        $movimentacaoRebanho = Helpers::processarColunasUpdate($movimentacaoRebanho, $request->all());

        if (!$movimentacaoRebanho->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $movimentacaoRebanho = \App\MovimentacaoRebanho::find($id);

        if (!$movimentacaoRebanho) {
            return response(['response' => 'MovimentacaoRebanho Não encontrado'], 400);
        }
        $movimentacaoRebanho->bo_ativo = false;
        if (!$movimentacaoRebanho->save()) {
            return response(['response' => 'Erro ao deletar MovimentacaoRebanho'], 400);
        }

        return response(['response' => 'MovimentacaoRebanho Inativado com sucesso']);
    }
}
