<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

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

        $month = Request::get('month');

        $query = self::where('transaction.id_user', auth('api')->user()->id)
            ->join('transaction_account', 'transaction_account.transaction_id', '=', 'transaction.id')
            ->join('category', 'transaction.id_category', '=', 'category.id')
            ->join('account', 'account.id', '=', 'transaction_account.account_id')
            ->select('transaction.*','category.icon', 'account.description as account');
            
        if ($month != 0) {
            $query->whereRaw("MONTH(transaction.created_at) = {$month}");
        }

        return $query->get();
    }
}
