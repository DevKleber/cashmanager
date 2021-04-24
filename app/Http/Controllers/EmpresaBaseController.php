<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class EmpresaBaseController extends Controller
{
    public function index()
    {
        $empresaBase = \App\EmpresaBase::where('bo_ativo', true)->get();
        if (!$empresaBase) {
            return response(['response' => 'Não existe EmpresaBase'], 400);
        }

        return response(['dados' => $empresaBase]);
    }

    public function store(Request $request)
    {
        $request['bo_ativo'] = true;
        $request['id_empresa'] = auth()->user()->id_empresa;

        $empresaBase = \App\EmpresaBase::create($request->all());
        if (!$empresaBase) {
            return  response(['response' => 'Erro ao salvar EmpresaBase'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $empresaBase]);
    }

    public function show($id)
    {
        $empresaBase = \App\EmpresaBase::find($id);
        if (!$empresaBase) {
            return response(['response' => 'Não existe EmpresaBase'], 400);
        }

        return response($empresaBase);
    }

    public function update(Request $request, $id)
    {
        $empresaBase = \App\EmpresaBase::find($id);

        if (!$empresaBase) {
            return response(['response' => 'EmpresaBase Não encontrado'], 400);
        }
        $empresaBase = Helpers::processarColunasUpdate($empresaBase, $request->all());

        if (!$empresaBase->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $empresaBase = \App\EmpresaBase::find($id);

        if (!$empresaBase) {
            return response(['response' => 'EmpresaBase Não encontrado'], 400);
        }
        $empresaBase->bo_ativo = false;
        if (!$empresaBase->save()) {
            return response(['response' => 'Erro ao deletar EmpresaBase'], 400);
        }

        return response(['response' => 'EmpresaBase Inativado com sucesso']);
    }
}
