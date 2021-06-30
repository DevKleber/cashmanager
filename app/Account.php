<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class Account extends Model
{
    protected $table = 'account';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'description', 'banking', 'current_balance', 'id_user'];

    public static function updateBalance($request)
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

    public static function getTrasactionsByIdAccount($id)
    {
        $month = Request::get('month');
        $month = $month + 1;
        $year = date('Y');

        return self::join('transaction_account', 'transaction_account.account_id', '=', 'account.id')
            ->join('transaction', 'transaction.id', '=', 'transaction_account.transaction_id')
            ->where('account.id', $id)
            ->whereRaw("MONTH(transaction.created_at) = {$month}")
            ->whereRaw("YEAR(transaction.created_at) = {$year}")
            ->orderBy("transaction.id", "desc")
            ->get()
        ;
    }

    public static function getBalanceBackByTransaction(int $id, Transaction $transaction, TransactionAccount $transactionAccount)
    {
        if (!$transactionAccount) {
            return false;
        }

        $account = self::find($transactionAccount->account_id);

        if ($transaction->is_income) {
            $ar['current_balance'] = $account->current_balance - $transaction->value;
        } else {
            $ar['current_balance'] = $account->current_balance + $transaction->value;
        }

        return $account->update($ar);
    }
}
