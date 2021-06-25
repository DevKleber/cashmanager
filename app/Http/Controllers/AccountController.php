<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = \App\Account::where('id_user', auth('api')->user()->id)
        ->where('is_active', true)
        ->get();

        if (!$accounts) {
            return response(['response' => 'Conta não encontrada'], 400);
        }

        return response($accounts);
    }

    public function store(Request $request)
    {
        $ar = $request->all();
        $ar['id_user'] = auth('api')->user()->id;

        $account = \App\Account::create($ar);

        if (!$account) {
            return  response(['message' => 'Erro ao salvar Conta'], 400);
        }

        return response($account);
    }

    public function show($id)
    {
        $account = \App\Account::find($id);

        if (!$account) {
            return response(['response' => 'Não existe Conta'], 400);
        }
        
        if ($account->id_user != auth('api')->user()->id) {
            return response(['error' => 'Não tem permissão para acessar essa Conta'], 400);
        }

        $account->items = \App\Account::getTrasactionsByIdAccount($id);

        return response($account);
    }

    public function update(Request $request, $id)
    {
        $account = \App\Account::find($id);

        
        if ($account) {
            if ($account['id_user'] != auth('api')->user()->id) {
                return response(['error' => 'Não tem permissão para alterar esse Conta'], 400);
            }

            $account = Helpers::processarColunasUpdate($account, $request->all());

            if (!$account->save()) {
                return response(['response' => 'Conta não foi atualizado'], 400);
            }

            return response($account);
        }

        return response(['response' => 'Conta não encontrado']);
    }

    public function destroy($id)
    {
        $account = \App\Account::find($id);

        if (!$account) {
            return response(['response' => 'Conta Não encontrado'], 400);
        }
        
        $account->is_active = false;

        if (!$account->save()) {
            return response(['response' => 'Erro ao deletar Conta'], 400);
        }

        return response(['response' => 'Conta Inativado com sucesso']);
    }
}
