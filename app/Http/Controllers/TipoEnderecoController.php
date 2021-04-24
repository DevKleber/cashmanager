<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class TipoEnderecoController extends Controller
{
    public function index()
    {
        $tipoEndereco = \App\TipoEndereco::where('bo_ativo', true)->orderBy('no_tipoendereco')->get();
        if (!$tipoEndereco) {
            return response(['response' => 'Não existe tipo endereço'], 400);
        }

        return response(['dados' => $tipoEndereco]);
    }

    public function store(Request $request)
    {
        $request['bo_ativo'] = true;

        $tipoEndereco = \App\TipoEndereco::create($request->all());
        if (!$tipoEndereco) {
            return  response(['response' => 'Erro ao salvar tipo endereço'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $tipoEndereco]);
    }

    public function show($id)
    {
        $tipoEndereco = \App\TipoEndereco::find($id);
        if (!$tipoEndereco) {
            return response(['response' => 'Não existe tipo endereço'], 400);
        }

        return response($tipoEndereco);
    }

    public function update(Request $request, $id)
    {
        $tipoEndereco = \App\TipoEndereco::find($id);

        if (!$tipoEndereco) {
            return response(['response' => 'Tipo endereço Não encontrado'], 400);
        }
        $tipoEndereco = Helpers::processarColunasUpdate($tipoEndereco, $request->all());

        if (!$tipoEndereco->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $tipoEndereco = \App\TipoEndereco::find($id);

        if (!$tipoEndereco) {
            return response(['response' => 'Tipo endereço Não encontrado'], 400);
        }
        $tipoEndereco->bo_ativo = false;
        if (!$tipoEndereco->save()) {
            return response(['response' => 'Erro ao deletar tipo endereço'], 400);
        }

        return response(['response' => 'Tipo endereço Inativado com sucesso']);
    }
}
