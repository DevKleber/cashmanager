<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    protected $table = 'credit_card';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'closing_day', 'due_day', 'id_user', ' is_active'];

    public static function getCreditCardById($id) 
    {
        $creditCard = self::where('id_user', $id)->first();

        if (!$creditCard) {
            return false;
        }

        $expenseCreditCard = self::join('expense_credit_card', 'credit_card.id', '=', 'expense_credit_card.id_credit_card')
            ->join('transaction', 'transaction.id', '=', 'expense_credit_card.id_transaction')
            ->join('category', 'category.id', '=', 'transaction.id_category')
            ->select('credit_card.transaction.*', 'category.icon')
            ->where('id_user', auth('api')->user()->id)
            ->where('credit_card.id', $creditCard->id)->get();
        
        $total = 0;
        foreach ($expenseCreditCard as $key => $value) {
            $total += $value->value;
        }

        $creditCard->items = $expenseCreditCard;
        $creditCard->total = $total;

        return $creditCard;
    }
}
