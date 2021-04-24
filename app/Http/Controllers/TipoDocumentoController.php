<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class TipoDocumentoController extends Controller
{
    public function index()
    {
        $tipoDocumento = \App\TipoDocumento::where('bo_ativo', true)->get();
        if (!$tipoDocumento) {
            return response(['response' => 'Não existe Tipo documento'], 400);
        }

        return response(['dados' => $tipoDocumento]);
    }

    public function store(Request $request)
    {
        $tipoDocumento = \App\TipoDocumento::create($request->all());
        if (!$tipoDocumento) {
            return  response(['response' => 'Erro ao salvar Tipo documento'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $tipoDocumento]);
    }

    public function show($id)
    {
        $tipoDocumento = \App\TipoDocumento::find($id);
        if (!$tipoDocumento) {
            return response(['response' => 'Não existe Tipo documento'], 400);
        }

        return response($tipoDocumento);
    }

    public function update(Request $request, $id)
    {
        $tipoDocumento = \App\TipoDocumento::find($id);

        if (!$tipoDocumento) {
            return response(['response' => 'TipoDocumento Não encontrado'], 400);
        }
        $tipoDocumento = Helpers::processarColunasUpdate($tipoDocumento, $request->all());

        if (!$tipoDocumento->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $tipoDocumento = \App\TipoDocumento::find($id);

        if (!$tipoDocumento) {
            return response(['response' => 'TipoDocumento Não encontrado'], 400);
        }
        $tipoDocumento->bo_ativo = false;
        if (!$tipoDocumento->save()) {
            return response(['response' => 'Erro ao deletar Tipo documento'], 400);
        }

        return response(['response' => 'TipoDocumento Inativado com sucesso']);
    }
}
