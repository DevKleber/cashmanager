<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;
use Validator;

class EmpresaController extends Controller
{
    public function show($id)
    {
        if ($id != auth()->user()->id_empresa) {
            return response(['response' => 'Você não tem permissão!'], 400);
        }

        $empresa = \App\Empresa::find($id);
        if (!$empresa) {
            return response(['response' => 'Empresa não localizada!'], 400);
        }

        return response($empresa);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ed_email' => 'email',
        ]);
        if ($id != auth()->user()->id_empresa) {
            return response(['response' => 'Você não tem permissão!'], 400);
        }

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }
        $request['nu_telefone'] = Helpers::removerCaracteresEspeciaisEspacos($request->nu_telefone);
        $request['nu_cpfcnpj'] = Helpers::removerCaracteresEspeciaisEspacos($request->nu_cpfcnpj);

        $empresa = \App\Empresa::find($id);
        if (!$empresa) {
            return response(['response' => 'Empresa não localizada!'], 400);
        }
        $empresa = Helpers::processarColunasUpdate($empresa, $request->all());

        if (!$empresa->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }
}
