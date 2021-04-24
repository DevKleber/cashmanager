<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;
use Validator;

class PropriedadeController extends Controller
{
    public function index()
    {
        $propriedade = \App\Propriedade::getAllProperty();
        if (!$propriedade) {
            return response(['response' => 'Não existe propriedade'], 400);
        }

        return response(['dados' => $propriedade]);
    }

    public function getAllPropertyActive()
    {
        $propriedade = \App\Propriedade::getAllPropertyActive();
        if (!$propriedade) {
            return response(['response' => 'Não existe propriedade'], 400);
        }

        return response(['dados' => $propriedade]);
    }

    public function getAllPropertyInactive()
    {
        $propriedade = \App\Propriedade::getAllPropertyInactive();
        if (!$propriedade) {
            return response(['response' => 'Não existe propriedade'], 400);
        }

        return response(['dados' => $propriedade]);
    }

    public function store(Request $request)
    {
        $request['nu_latitude'] = Helpers::removerCaracteresEspeciaisEspacos($request['nu_latitude']);
        $request['nu_longitude'] = Helpers::removerCaracteresEspeciaisEspacos($request['nu_longitude']);
        $request['nu_inscricaoestadual'] = Helpers::removerCaracteresEspeciaisEspacos($request['nu_inscricaoestadual']);
        $request['id_empresa'] = auth()->user()->id_empresa;

        $validator = Validator::make($request->all(), [
            'nu_latitude' => 'required|size:7',
            'nu_longitude' => 'required|size:7',
            'vl_area' => 'required',
            'nu_inscricaoestadual' => 'required',
            'no_propriedade' => 'required|string',
            'id_localidade' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        if (\App\Propriedade::IeExist($request['nu_inscricaoestadual'])) {
            return  response(['response' => 'IE informado já consta em nossa base de dados'], 400);
        }

        $propriedade = \App\Propriedade::create($request->all());
        if (!$propriedade) {
            return  response(['response' => 'Erro ao salvar propriedade'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $propriedade]);
    }

    public function show($id)
    {
        $propriedade = \App\Propriedade::getProperty($id);
        if (!$propriedade) {
            return response(['response' => 'Não existe propriedade'], 400);
        }

        return response($propriedade);
    }

    public function update(Request $request, $id)
    {
        $request['nu_latitude'] = Helpers::removerCaracteresEspeciaisEspacos($request['nu_latitude']);
        $request['nu_longitude'] = Helpers::removerCaracteresEspeciaisEspacos($request['nu_longitude']);
        $request['nu_inscricaoestadual'] = Helpers::removerCaracteresEspeciaisEspacos($request['nu_inscricaoestadual']);
        $request['id_empresa'] = auth()->user()->id_empresa;

        $validator = Validator::make($request->all(), [
            'nu_latitude' => 'required|size:7',
            'nu_longitude' => 'required|size:7',
            'vl_area' => 'required',
            'nu_inscricaoestadual' => 'required',
            'no_propriedade' => 'required|string',
            'id_localidade' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $propriedade = \App\Propriedade::find($id);

        if (\App\Propriedade::IeExist($request['nu_inscricaoestadual'], $propriedade->id_propriedade)) {
            return  response(['response' => 'IE informado já consta em nossa base de dados'], 400);
        }

        if (!$propriedade) {
            return response(['response' => 'Propriedade Não encontrada'], 400);
        }
        $propriedade = Helpers::processarColunasUpdate($propriedade, $request->all());

        if (!$propriedade->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $propriedade = \App\Propriedade::find($id);

        if (!$propriedade) {
            return response(['response' => 'Propriedade Não encontrada'], 400);
        }
        $propriedade->bo_ativo = false;
        if (!$propriedade->save()) {
            return response(['response' => 'Erro ao deletar propriedade'], 400);
        }

        return response(['response' => 'Propriedade Inativada com sucesso']);
    }
}
