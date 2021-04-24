<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index()
    {
        $documento = \App\Documento::all();
        if (!$documento) {
            return response(['response' => 'Não existe Documento'], 400);
        }

        return response(['dados' => $documento]);
    }

    public function store(Request $request)
    {
        $documento = \App\Documento::create($request->all());
        if (!$documento) {
            return  response(['response' => 'Erro ao salvar Documento'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $documento]);
    }

    public function show($id)
    {
        $documento = \App\Documento::find($id);
        if (!$documento) {
            return response(['response' => 'Não existe Documento'], 400);
        }

        return response($documento);
    }

    public function update(Request $request, $id)
    {
        $documento = \App\Documento::find($id);

        if (!$documento) {
            return response(['response' => 'Documento Não encontrado'], 400);
        }
        $documento = Helpers::processarColunasUpdate($documento, $request->all());

        if (!$documento->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $documento = \App\Documento::find($id);

        if (!$documento) {
            return response(['response' => 'Documento Não encontrado'], 400);
        }
        $documento->bo_ativo = false;
        if (!$documento->save()) {
            return response(['response' => 'Erro ao deletar Documento'], 400);
        }

        return response(['response' => 'Documento Inativado com sucesso']);
    }
}
