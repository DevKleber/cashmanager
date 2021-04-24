<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class StatusFuncionarioController extends Controller
{
    public function index()
    {
        $statusFuncionario = \App\StatusFuncionario::where('bo_ativo', true)->get();
        if (!$statusFuncionario) {
            return response(['response' => 'Não existe status funcionário'], 400);
        }

        return response(['dados' => $statusFuncionario]);
    }

    public function store(Request $request)
    {
        $request['bo_ativo'] = true;

        $statusFuncionario = \App\StatusFuncionario::create($request->all());
        if (!$statusFuncionario) {
            return  response(['response' => 'Erro ao salvar status funcionário'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $statusFuncionario]);
    }

    public function show($id)
    {
        $statusFuncionario = \App\StatusFuncionario::find($id);
        if (!$statusFuncionario) {
            return response(['response' => 'Não existe status funcionário'], 400);
        }

        return response($statusFuncionario);
    }

    public function update(Request $request, $id)
    {
        $statusFuncionario = \App\StatusFuncionario::find($id);

        if (!$statusFuncionario) {
            return response(['response' => 'Status funcionário não encontrado'], 400);
        }
        $statusFuncionario = Helpers::processarColunasUpdate($statusFuncionario, $request->all());

        if (!$statusFuncionario->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $statusFuncionario = \App\StatusFuncionario::find($id);

        if (!$statusFuncionario) {
            return response(['response' => 'StatusFuncionario Não encontrado'], 400);
        }
        $statusFuncionario->bo_ativo = false;
        if (!$statusFuncionario->save()) {
            return response(['response' => 'Erro ao deletar status funcionário'], 400);
        }

        return response(['response' => 'StatusFuncionario Inativado com sucesso']);
    }
}
