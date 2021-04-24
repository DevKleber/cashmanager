<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class TipoTelefoneController extends Controller
{
    public function index()
    {
        $tipoTelefone = \App\TipoTelefone::where('bo_ativo', true)->orderBy('ds_tipotelefone')->get();
        if (!$tipoTelefone) {
            return response(['response' => 'Não existe tipo telefone'], 400);
        }

        return response(['dados' => $tipoTelefone]);
    }

    public function store(Request $request)
    {
        $request['bo_ativo'] = true;

        $tipoTelefone = \App\TipoTelefone::create($request->all());
        if (!$tipoTelefone) {
            return  response(['response' => 'Erro ao salvar tipo telefone'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $tipoTelefone]);
    }

    public function show($id)
    {
        $tipoTelefone = \App\TipoTelefone::find($id);
        if (!$tipoTelefone) {
            return response(['response' => 'Não existe tipo telefone'], 400);
        }

        return response($tipoTelefone);
    }

    public function update(Request $request, $id)
    {
        $tipoTelefone = \App\TipoTelefone::find($id);

        if (!$tipoTelefone) {
            return response(['response' => 'Tipo telefone Não encontrado'], 400);
        }
        $tipoTelefone = Helpers::processarColunasUpdate($tipoTelefone, $request->all());

        if (!$tipoTelefone->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $tipoTelefone = \App\TipoTelefone::find($id);

        if (!$tipoTelefone) {
            return response(['response' => 'Tipo telefone Não encontrado'], 400);
        }

        $tipoTelefone->bo_ativo = false;
        if (!$tipoTelefone->save()) {
            return response(['response' => 'Erro ao deletar tipo telefone'], 400);
        }

        return response(['response' => 'Tipo telefone inativado com sucesso']);
    }
}
