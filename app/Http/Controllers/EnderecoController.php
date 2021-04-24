<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class EnderecoController extends Controller
{
    public function index()
    {
        $endereco = \App\Endereco::all();
        if (!$endereco) {
            return response(['response' => 'Não existe Endereco'], 400);
        }

        return response(['dados' => $endereco]);
    }

    public function store(Request $request)
    {
        $endereco = \App\Endereco::create($request->all());
        if (!$endereco) {
            return  response(['response' => 'Erro ao salvar Endereco'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $endereco]);
    }

    public function show($id)
    {
        $endereco = \App\Endereco::find($id);
        if (!$endereco) {
            return response(['response' => 'Não existe Endereco'], 400);
        }

        return response($endereco);
    }

    public function update(Request $request, $id)
    {
        $endereco = \App\Endereco::find($id);

        if (!$endereco) {
            return response(['response' => 'Endereco Não encontrado'], 400);
        }
        $endereco = Helpers::processarColunasUpdate($endereco, $request->all());

        if (!$endereco->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $endereco = \App\Endereco::find($id);

        if (!$endereco) {
            return response(['response' => 'Endereco Não encontrado'], 400);
        }
        $endereco->bo_ativo = false;
        if (!$endereco->save()) {
            return response(['response' => 'Erro ao deletar Endereco'], 400);
        }

        return response(['response' => 'Endereco Inativado com sucesso']);
    }
}
