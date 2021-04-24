<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class OccupationController extends Controller
{
    public function returnIndex($occupation)
    {
        if (!$occupation) {
            return response(['response' => 'Cargo não encontrado'], 400);
        }

        return response(['dados' => $occupation]);
    }

    public function index()
    {
        return $this->returnIndex(\App\Occupation::getOccupation());
    }

    public function getOccupationActive()
    {
        return $this->returnIndex(\App\Occupation::getOccupation(['bo_ativo' => true]));
    }

    public function getOccupationInactive()
    {
        return $this->returnIndex(\App\Occupation::getOccupation(['bo_ativo' => false]));
    }

    public function store(Request $request)
    {
        $occupation = \App\Occupation::create($request->all());
        if (!$occupation) {
            return  response(['response' => 'Erro ao salvar'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $occupation]);
    }

    public function show($id)
    {
        $occupation = \App\Occupation::find($id);
        if (!$occupation) {
            return response(['response' => 'Não existe cargo'], 400);
        }

        return response($occupation);
    }

    public function update(Request $request, $id)
    {
        $occupation = \App\Occupation::find($id);

        if (!$occupation) {
            return response(['response' => 'Cargo Não encontrado'], 400);
        }
        $occupation = Helpers::processarColunasUpdate($occupation, $request->all());

        if (!$occupation->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $occupation = \App\Occupation::find($id);

        if (!$occupation) {
            return response(['response' => 'Cargo Não encontrado'], 400);
        }
        $occupation->bo_ativo = false;
        if (!$occupation->save()) {
            return response(['response' => 'Erro ao inativar Cargo'], 400);
        }

        return response(['response' => 'Cargo Inativado com sucesso']);
    }
}
