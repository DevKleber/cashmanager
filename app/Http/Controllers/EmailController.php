<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index()
    {
        $email = \App\Email::all();
        if (!$email) {
            return response(['response' => 'Não existe Email'], 400);
        }

        return response(['dados' => $email]);
    }

    public function store(Request $request)
    {
        $email = \App\Email::create($request->all());
        if (!$email) {
            return  response(['response' => 'Erro ao salvar Email'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $email]);
    }

    public function show($id)
    {
        $email = \App\Email::find($id);
        if (!$email) {
            return response(['response' => 'Não existe Email'], 400);
        }

        return response($email);
    }

    public function update(Request $request, $id)
    {
        $email = \App\Email::find($id);

        if (!$email) {
            return response(['response' => 'Email Não encontrado'], 400);
        }
        $email = Helpers::processarColunasUpdate($email, $request->all());

        if (!$email->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $email = \App\Email::find($id);

        if (!$email) {
            return response(['response' => 'Email Não encontrado'], 400);
        }
        $email->bo_ativo = false;
        if (!$email->save()) {
            return response(['response' => 'Erro ao deletar Email'], 400);
        }

        return response(['response' => 'Email Inativado com sucesso']);
    }
}
