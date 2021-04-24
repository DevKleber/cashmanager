<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class PessoaTelefoneController extends Controller
{
    public function index()
    {
        $pessoaTelefone = \App\PessoaTelefone::all();
        if (!$pessoaTelefone) {
            return response(['response' => 'Não existe PessoaTelefone'], 400);
        }

        return response(['dados' => $pessoaTelefone]);
    }

    public function store(Request $request)
    {
        $pessoaTelefone = \App\PessoaTelefone::create($request->all());
        if (!$pessoaTelefone) {
            return  response(['response' => 'Erro ao salvar PessoaTelefone'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $pessoaTelefone]);
    }

    public function show($id)
    {
        $pessoaTelefone = \App\PessoaTelefone::find($id);
        if (!$pessoaTelefone) {
            return response(['response' => 'Não existe PessoaTelefone'], 400);
        }

        return response($pessoaTelefone);
    }

    public function update(Request $request, $id)
    {
        $pessoaTelefone = \App\PessoaTelefone::find($id);

        if (!$pessoaTelefone) {
            return response(['response' => 'PessoaTelefone Não encontrado'], 400);
        }
        $pessoaTelefone = Helpers::processarColunasUpdate($pessoaTelefone, $request->all());

        if (!$pessoaTelefone->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $pessoaTelefone = \App\PessoaTelefone::find($id);

        if (!$pessoaTelefone) {
            return response(['response' => 'PessoaTelefone Não encontrado'], 400);
        }
        $pessoaTelefone->bo_ativo = false;
        if (!$pessoaTelefone->save()) {
            return response(['response' => 'Erro ao deletar PessoaTelefone'], 400);
        }

        return response(['response' => 'PessoaTelefone Inativado com sucesso']);
    }
}
