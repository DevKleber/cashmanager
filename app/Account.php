<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\Transaction;

class Account extends Model
{
    protected $table = 'account';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'description', 'id_banking', 'current_balance', 'id_user'];

    public static function alterBalance($request)
    {
        if (!isset($request['account_id'])) {
            return true;
        }

        $account = self::find($request['account_id']);

        if (!$account) {
            return false;
        }

        if ($request['is_income']) {
            $ar['current_balance'] = $account->current_balance + $request['value'];
        } else {
            $ar['current_balance'] = $account->current_balance - $request['value'];
        }

        return $account->update($ar);
    }

    public static function returnBalanceByTransaction(int $id, Transaction $transaction)
    {

        $transactionAccount = \App\TransactionAccount::where('transaction_id', $id)->first();

        if (!$transactionAccount) {
            return false;
        }

        $account = self::find($transactionAccount->transaction_id);


        if ($transaction->is_income) {
            $ar['current_balance'] = $account->current_balance - $transaction->value;
        } else {
            $ar['current_balance'] = $account->current_balance + $transaction->value;
        }

        return $account->update($ar);
    }
}