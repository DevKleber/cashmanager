<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transaction = \App\Transaction::getTransactions();

        if (!$transaction) {
            return response(['response' => 'Transação não encontrada'], 400);
        }

        return response($transaction);
    }

    public function store(Request $request)
    {
        \DB::beginTransaction();

        $ar = $request->all();
        $ar['id_user'] = auth('api')->user()->id;

        $transaction = \App\Transaction::create($ar);

        if (!$transaction) {
            return response(['message' => 'Erro ao salvar Transação'], 400);
        }

        $arTransactionAccount['account_id'] = $ar['id_account'];
        $arTransactionAccount['transaction_id'] = $transaction->id;

        $transactionAccount = \App\TransactionAccount::create($arTransactionAccount);

        if (!$transactionAccount) {
            return response(['message' => 'Erro ao salvar transaction account'], 400);
        }

        if (!\App\TransactionItem::saveItens($ar, $transaction)) {
            return response(['message' => 'Erro ao salvar itens'], 400);
        }

        if (!\App\Account::updateBalance($ar)) {
            return response(['message' => 'Erro ao alterar valor da conta'], 400);
        }

        \DB::commit();

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

        if (!\App\Account::getBalanceBackByTransaction($id, $transaction)) {
            return response(['response' => 'Erro ao deletar Transação'], 400);
        }

        return response(['response' => 'Transação Inativado com sucesso']);
    }
}
