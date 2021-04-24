<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class TipoEmailController extends Controller
{
    public function index()
    {
        $tipoEmail = \App\TipoEmail::where('bo_ativo', true)->get();
        if (!$tipoEmail) {
            return response(['response' => 'Não existe tipo e-mail'], 400);
        }

        return response(['dados' => $tipoEmail]);
    }

    public function store(Request $request)
    {
        $request['bo_ativo'] = true;

        $tipoEmail = \App\TipoEmail::create($request->all());
        if (!$tipoEmail) {
            return  response(['response' => 'Erro ao salvar tipo e-mail'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $tipoEmail]);
    }

    public function show($id)
    {
        $tipoEmail = \App\TipoEmail::find($id);
        if (!$tipoEmail) {
            return response(['response' => 'Não existe tipo e-mail'], 400);
        }

        return response($tipoEmail);
    }

    public function update(Request $request, $id)
    {
        $tipoEmail = \App\TipoEmail::find($id);

        if (!$tipoEmail) {
            return response(['response' => 'Tipo e-mail Não encontrado'], 400);
        }
        $tipoEmail = Helpers::processarColunasUpdate($tipoEmail, $request->all());

        if (!$tipoEmail->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $tipoEmail = \App\TipoEmail::find($id);

        if (!$tipoEmail) {
            return response(['response' => 'TipoEmail Não encontrado'], 400);
        }
        $tipoEmail->bo_ativo = false;
        if (!$tipoEmail->save()) {
            return response(['response' => 'Erro ao deletar tipo e-mail'], 400);
        }

        return response(['response' => 'TipoEmail Inativado com sucesso']);
    }
}
