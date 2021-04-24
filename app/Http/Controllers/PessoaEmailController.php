<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class PessoaEmailController extends Controller
{
    public function index()
    {
        $pessoaEmail = \App\PessoaEmail::all();
        if (!$pessoaEmail) {
            return response(['response' => 'Não existe PessoaEmail'], 400);
        }

        return response(['dados' => $pessoaEmail]);
    }

    public function store(Request $request)
    {
        $pessoaEmail = \App\PessoaEmail::create($request->all());
        if (!$pessoaEmail) {
            return  response(['response' => 'Erro ao salvar PessoaEmail'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $pessoaEmail]);
    }

    public function show($id)
    {
        $pessoaEmail = \App\PessoaEmail::find($id);
        if (!$pessoaEmail) {
            return response(['response' => 'Não existe PessoaEmail'], 400);
        }

        return response($pessoaEmail);
    }

    public function update(Request $request, $id)
    {
        $pessoaEmail = \App\PessoaEmail::find($id);

        if (!$pessoaEmail) {
            return response(['response' => 'PessoaEmail Não encontrado'], 400);
        }
        $pessoaEmail = Helpers::processarColunasUpdate($pessoaEmail, $request->all());

        if (!$pessoaEmail->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $pessoaEmail = \App\PessoaEmail::find($id);

        if (!$pessoaEmail) {
            return response(['response' => 'PessoaEmail Não encontrado'], 400);
        }
        $pessoaEmail->bo_ativo = false;
        if (!$pessoaEmail->save()) {
            return response(['response' => 'Erro ao deletar PessoaEmail'], 400);
        }

        return response(['response' => 'PessoaEmail Inativado com sucesso']);
    }
}
