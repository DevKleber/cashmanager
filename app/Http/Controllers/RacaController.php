<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class RacaController extends Controller
{
    public function index()
    {
        $raca = \App\Raca::where('bo_ativo', true)->get();
        if (!$raca) {
            return response(['response' => 'Não existe raça'], 400);
        }

        return response(['dados' => $raca]);
    }

    public function getBreedBySpecies($specie)
    {
        $raca = \App\Raca::where('id_especie', $specie)->where('bo_ativo', true)->get();
        if (!$raca) {
            return response(['response' => 'Não existe raça'], 400);
        }

        return response(['dados' => $raca]);
    }

    public function store(Request $request)
    {
        $raca = \App\Raca::create($request->all());
        if (!$raca) {
            return  response(['response' => 'Erro ao salvar a raça'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $raca]);
    }

    public function show($id)
    {
        $raca = \App\Raca::find($id);
        if (!$raca) {
            return response(['response' => 'Não existe raça'], 400);
        }

        return response($raca);
    }

    public function update(Request $request, $id)
    {
        $raca = \App\Raca::find($id);

        if (!$raca) {
            return response(['response' => 'Raça não encontrada'], 400);
        }
        $raca = Helpers::processarColunasUpdate($raca, $request->all());

        if (!$raca->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $raca = \App\Raca::find($id);

        if (!$raca) {
            return response(['response' => 'Raça não encontrada'], 400);
        }
        $raca->bo_ativo = false;
        if (!$raca->save()) {
            return response(['response' => 'Erro ao deletar raça'], 400);
        }

        return response(['response' => 'Raca inativada com sucesso']);
    }
}
