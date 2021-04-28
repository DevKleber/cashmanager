<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transaction = \App\Transaction::where('id_user', auth('api')->user()->id)->get();

        if (!$transaction) {
            return response(['response' => 'Transação não encontrada'], 400);
        }

        return response($transaction);
    }

    public function store(Request $request)
    {
        $ar = $request->all();
        $ar['id_user'] = auth('api')->user()->id;
        $ar['date'] = date('Y-m-d H:i');

        $transaction = \App\Transaction::create($ar);

        if (!$transaction) {
            return  response(['message' => 'Erro ao salvar Transação'], 400);
        }

        return response($transaction);
    }

    public function show($id)
    {
        $transaction = \App\Transaction::getTransactionById($id);

        if (!$transaction) {
            return response(['response' => 'Não existe Transação'], 400);
        }

        return response($transaction);
    }

    public function update(Request $request, $id)
    {
        $transaction = \App\Transaction::find($id);

        
        if ($transaction) {
            if ($transaction['id_user'] != auth('api')->user()->id) {
                return response(['error' => 'Não tem permissão para alterar esse Transação'], 400);
            }

            $transaction = Helpers::processarColunasUpdate($transaction, $request->all());

            if (!$transaction->save()) {
                return response(['response' => 'Transação não foi atualizado'], 400);
            }

            return response($transaction);
        }

        return response(['response' => 'Transação não encontrado']);
    }

    public function destroy($id)
    {
        $transaction = \App\Transaction::find($id);

        if (!$transaction) {
            return response(['response' => 'Transação Não encontrado'], 400);
        }
        
        $transaction->is_active = false;

        if (!$transaction->save()) {
            return response(['response' => 'Erro ao deletar Transação'], 400);
        }

        return response(['response' => 'Transação Inativado com sucesso']);
    }
}
