<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class NucleoController extends Controller
{
    public function index()
    {
        $nucleo = \App\Nucleo::getAllNucleo();
        if (!$nucleo) {
            return response(['response' => 'Não existe Nucleo'], 400);
        }

        return response(['dados' => $nucleo]);
    }

    public function getCoreByProperty($id)
    {
        $nucleo = \App\Nucleo::join('rebanho.especie as e', 'rebanho.nucleo.id_especie', '=', 'e.id_especie')
            ->where('id_propriedade', $id)
            ->get()
        ;
        if (!$nucleo) {
            return response(['response' => 'Não existe Nucleo'], 400);
        }

        return response(['dados' => $nucleo]);
    }

    public function store(Request $request)
    {
        return false;
        $nucleo = \App\Nucleo::create($request->all());
        if (!$nucleo) {
            return  response(['response' => 'Erro ao salvar Nucleo'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $nucleo]);
    }

    public function show($id)
    {
        $nucleo = \App\Nucleo::join('rebanho.especie as e', 'e.id_especie', '=', 'rebanho.nucleo.id_especie')
            ->join('propriedade.propriedade as p', 'p.id_propriedade', '=', 'rebanho.nucleo.id_propriedade')
            ->where('id_nucleo', $id)
            ->select('rebanho.nucleo.*', 'p.no_propriedade', 'e.no_especie')
            ->first()
        ;
        if (!$nucleo) {
            return response(['response' => 'Não existe Nucleo'], 400);
        }

        return response($nucleo);
    }

    public function update(Request $request, $id)
    {
        $nucleo = \App\Nucleo::find($id);

        if (!$nucleo) {
            return response(['response' => 'Nucleo Não encontrado'], 400);
        }
        $nucleo = Helpers::processarColunasUpdate($nucleo, $request->all());

        if (!$nucleo->update()) {
            return response(['response' => 'Erro ao alterar núcleo'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $nucleo = \App\Nucleo::find($id);

        if (!$nucleo) {
            return response(['response' => 'Nucleo Não encontrado'], 400);
        }
        $nucleo->bo_ativo = false;
        if (!$nucleo->save()) {
            return response(['response' => 'Erro ao deletar Nucleo'], 400);
        }

        return response(['response' => 'Nucleo Inativado com sucesso']);
    }
}
