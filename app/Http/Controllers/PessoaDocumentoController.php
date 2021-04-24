<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class PessoaDocumentoController extends Controller
{
    public function index()
    {
        $pessoaDocumento = \App\PessoaDocumento::all();
        if (!$pessoaDocumento) {
            return response(['response' => 'Não existe PessoaDocumento'], 400);
        }

        return response(['dados' => $pessoaDocumento]);
    }

    public function store(Request $request)
    {
        $pessoaDocumento = \App\PessoaDocumento::create($request->all());
        if (!$pessoaDocumento) {
            return  response(['response' => 'Erro ao salvar PessoaDocumento'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $pessoaDocumento]);
    }

    public function show($id)
    {
        $pessoaDocumento = \App\PessoaDocumento::find($id);
        if (!$pessoaDocumento) {
            return response(['response' => 'Não existe PessoaDocumento'], 400);
        }

        return response($pessoaDocumento);
    }

    public function update(Request $request, $id)
    {
        $pessoaDocumento = \App\PessoaDocumento::find($id);

        if (!$pessoaDocumento) {
            return response(['response' => 'PessoaDocumento Não encontrado'], 400);
        }
        $pessoaDocumento = Helpers::processarColunasUpdate($pessoaDocumento, $request->all());

        if (!$pessoaDocumento->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $pessoaDocumento = \App\PessoaDocumento::find($id);

        if (!$pessoaDocumento) {
            return response(['response' => 'PessoaDocumento Não encontrado'], 400);
        }
        $pessoaDocumento->bo_ativo = false;
        if (!$pessoaDocumento->save()) {
            return response(['response' => 'Erro ao deletar PessoaDocumento'], 400);
        }

        return response(['response' => 'PessoaDocumento Inativado com sucesso']);
    }
}
