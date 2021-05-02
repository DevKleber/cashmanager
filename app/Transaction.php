<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'value', 'description', 'id_user', 'is_income', 'name'];

    public static function getTransactionById(int $id)
    {
        $transaction = self::where('id_user', auth('api')->user()->id)
            ->where('id', $id)
            ->first();

        if (!$transaction) {
            return false;
        }
        
        $transaction = $transaction->toArray();
        $transaction['itens'] = \App\TransactionItem::getItensByIdTransaction($id);

        return $transaction;
    }

    public static function getTransactions()
    {
        return self::where('transaction.id_user', auth('api')->user()->id)
            ->join('transaction_account', 'transaction_account.transaction_id', '=', 'transaction.id')
            ->join('account', 'account.id', '=', 'transaction_account.account_id')
            ->select('transaction.*', 'account.description as account')
            ->get();
    }
}
