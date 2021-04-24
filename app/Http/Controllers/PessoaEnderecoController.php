<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class PessoaEnderecoController extends Controller
{
    public function index()
    {
        $pessoaEndereco = \App\PessoaEndereco::all();
        if (!$pessoaEndereco) {
            return response(['response' => 'Não existe PessoaEndereco'], 400);
        }

        return response(['dados' => $pessoaEndereco]);
    }

    public function store(Request $request)
    {
        $pessoaEndereco = \App\PessoaEndereco::create($request->all());
        if (!$pessoaEndereco) {
            return  response(['response' => 'Erro ao salvar PessoaEndereco'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $pessoaEndereco]);
    }

    public function show($id)
    {
        $pessoaEndereco = \App\PessoaEndereco::find($id);
        if (!$pessoaEndereco) {
            return response(['response' => 'Não existe PessoaEndereco'], 400);
        }

        return response($pessoaEndereco);
    }

    public function update(Request $request, $id)
    {
        $pessoaEndereco = \App\PessoaEndereco::find($id);

        if (!$pessoaEndereco) {
            return response(['response' => 'PessoaEndereco Não encontrado'], 400);
        }
        $pessoaEndereco = Helpers::processarColunasUpdate($pessoaEndereco, $request->all());

        if (!$pessoaEndereco->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $pessoaEndereco = \App\PessoaEndereco::find($id);

        if (!$pessoaEndereco) {
            return response(['response' => 'PessoaEndereco Não encontrado'], 400);
        }
        $pessoaEndereco->bo_ativo = false;
        if (!$pessoaEndereco->save()) {
            return response(['response' => 'Erro ao deletar PessoaEndereco'], 400);
        }

        return response(['response' => 'PessoaEndereco Inativado com sucesso']);
    }
}
