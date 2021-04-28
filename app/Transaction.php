<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transaction';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'value', 'date', 'description', 'id_user', 'is_income', 'name'];

    public static function getTransactionById(int $id)
    {
        $transaction = self::where('id_user', auth('api')->user()->id)
            ->where('id', $id)
            ->get();

        if (!$transaction) {
            return false;
        }
        
        $transaction = $transaction->toArray();
        $transaction['itens'] = \App\TransctionItem::getItensByIdTransaction($id);

        return $transaction;
    }
}