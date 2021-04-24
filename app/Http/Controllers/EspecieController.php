<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class EspecieController extends Controller
{
    public function getSpecies()
    {
        $species = \App\Especie::where('bo_ativo', true)
            ->Join('sistema.arquivo as a', 'a.id_arquivo', '=', 'especie.id_arquivo')
            ->orderBy('no_especie')
            ->select('especie.*', 'a.mm_caminho', 'a.mm_caminho_miniatura')
            ->get()
        ;

        if (!$species) {
            return response(['response' => 'Não existe espécie'], 400);
        }

        return response(['dados' => $species]);
    }

    public function index()
    {
        $species = \App\Especie::where('bo_ativo', true)
            ->Join('sistema.arquivo as a', 'a.id_arquivo', '=', 'especie.id_arquivo')
            ->orderBy('no_especie')
            ->select('especie.*', 'a.mm_caminho', 'a.mm_caminho_miniatura')
            ->get()
        ;

        if (!$species) {
            return response(['response' => 'Não existe espécie'], 400);
        }

        $ar = [];
        foreach ($species as $key => $value) {
            $ar[$key] = $value;
            $ar[$key]['estratificacao'] = \App\Estratificacao::where('id_especie', $value->id_especie)->orderBy('ordem')->get();
        }

        return response(['dados' => $ar]);
    }

    public function store(Request $request)
    {
        $request['bo_ativo'] = true;

        $species = \App\Especie::create($request->all());
        if (!$species) {
            return  response(['response' => 'Erro ao salvar espécie'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $species]);
    }

    public function show($id)
    {
        $species = \App\Especie::find($id);
        if (!$species) {
            return response(['response' => 'Não existe espécie'], 400);
        }

        return response($species);
    }

    public function update(Request $request, $id)
    {
        $species = \App\Especie::find($id);

        if (!$species) {
            return response(['response' => 'Especie Não encontrado'], 400);
        }
        $species = Helpers::processarColunasUpdate($species, $request->all());

        if (!$species->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $species = \App\Especie::find($id);

        if (!$species) {
            return response(['response' => 'Especie Não encontrado'], 400);
        }
        $species->bo_ativo = false;
        if (!$species->save()) {
            return response(['response' => 'Erro ao deletar espécie'], 400);
        }

        return response(['response' => 'Especie Inativado com sucesso']);
    }
}
