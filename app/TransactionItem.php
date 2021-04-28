<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $table = 'transaction_item';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'is_paid', 'due_date', 'value', 'currenct_installment', 'installment', 'id_transaction'];

    public static function getItensByIdTransaction(int $id)
    {
        return self::where('transaction_id', $id)->get();
    }
}