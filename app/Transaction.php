<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class Transaction extends Model
{
    protected $table = 'transaction';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'value', 'description', 'id_user', 'is_income', 'name', 'id_category'];

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
        $month = Request::get('month');
        $month = $month + 1;
        $query = \App\TransactionItem::join('transaction as t', 't.id', '=', 'transaction_item.id_transaction')
            ->join('transaction_account', 'transaction_account.transaction_id', '=', 't.id')
            ->join('category', 't.id_category', '=', 'category.id')
            ->join('account', 'account.id', '=', 'transaction_account.account_id')
            ->select(
                't.*',
                'category.icon',
               'category.name as name_category',
               'account.description as account'
            )
            ->where('t.id_user', auth('api')->user()->id)
        ;

        $query->whereRaw("MONTH(transaction_item.due_date) = {$month}");

        return $query->get();
    }
}
