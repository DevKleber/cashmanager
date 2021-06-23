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

        if ($ar['id_account']) {
            $arTransactionAccount['account_id'] = $ar['id_account'];
            $arTransactionAccount['transaction_id'] = $transaction->id;

            $transactionAccount = \App\TransactionAccount::create($arTransactionAccount);

            if (!$transactionAccount) {
                return response(['message' => 'Erro ao salvar transaction account'], 400);
            }

            $account = \App\Account::find($ar['id_account']);
            
            if ($ar['is_income']) {
                $account->current_balance = $account->current_balance + $transaction->value;
            } else {
                $account->current_balance = $account->current_balance - $transaction->value;
            }

            $account->save();
        }

        if ($ar['id_creditcard']) {
            $arExpenseCreditCard['id_credit_card'] = $ar['id_creditcard'];
            $arExpenseCreditCard['id_transaction'] = $transaction->id;

            $expenseCreditCard = \App\ExpenseCreditCard::create($arExpenseCreditCard);

            if (!$expenseCreditCard) {
                return response(['message' => 'Erro ao salvar transaction credit card'], 400);
            }
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
        $transaction = \App\Transaction::getDetailTransactionById($id);

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
        \DB::beginTransaction();
        $transaction = \App\Transaction::find($id);
        $transactionAccount = \App\TransactionAccount::find($transaction->id);

        if ($transaction->id_user !== auth('api')->user()->id) {
            return response(['response' => 'Sem permissão'], 400);
        }

        \App\TransactionItem::where('id_transaction', $transaction->id)->delete();

        if ($transactionAccount) {
            $transactionAccount->delete();

            if (!\App\Account::getBalanceBackByTransaction($id, $transaction, $transactionAccount)) {
                return response(['response' => 'Erro ao deletar Movimentação'], 400);
            }
        }
        $transaction->delete();

        // \DB::rollback();
        \DB::commit();

        return response(['response' => 'Movimentação deletada com sucesso']);
    }
}
