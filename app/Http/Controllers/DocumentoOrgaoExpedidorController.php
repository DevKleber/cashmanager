<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class DocumentoOrgaoExpedidorController extends Controller
{
    public function index()
    {
        $documentoOrgaoExpedidor = \App\DocumentoOrgaoExpedidor::where('bo_ativo', true)->get();
        if (!$documentoOrgaoExpedidor) {
            return response(['response' => 'Não existe orgão expedidor'], 400);
        }

        return response(['dados' => $documentoOrgaoExpedidor]);
    }

    public function store(Request $request)
    {
        $documentoOrgaoExpedidor = \App\DocumentoOrgaoExpedidor::create($request->all());
        if (!$documentoOrgaoExpedidor) {
            return  response(['response' => 'Erro ao salvar orgão expedidor'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $documentoOrgaoExpedidor]);
    }

    public function show($id)
    {
        $documentoOrgaoExpedidor = \App\DocumentoOrgaoExpedidor::find($id);
        if (!$documentoOrgaoExpedidor) {
            return response(['response' => 'Não existe orgão expedidor'], 400);
        }

        return response($documentoOrgaoExpedidor);
    }

    public function update(Request $request, $id)
    {
        $documentoOrgaoExpedidor = \App\DocumentoOrgaoExpedidor::find($id);

        if (!$documentoOrgaoExpedidor) {
            return response(['response' => 'orgão expedidor Não encontrado'], 400);
        }
        $documentoOrgaoExpedidor = Helpers::processarColunasUpdate($documentoOrgaoExpedidor, $request->all());

        if (!$documentoOrgaoExpedidor->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $documentoOrgaoExpedidor = \App\DocumentoOrgaoExpedidor::find($id);

        if (!$documentoOrgaoExpedidor) {
            return response(['response' => 'Orgão expedidor não encontrado'], 400);
        }
        $documentoOrgaoExpedidor->bo_ativo = false;
        if (!$documentoOrgaoExpedidor->save()) {
            return response(['response' => 'Erro ao deletar orgão expedidor'], 400);
        }

        return response(['response' => 'Orgão expedidor Inativado com sucesso']);
    }
}
