<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
class CreditCard extends Model
{
    protected $table = 'credit_card';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'closing_day', 'due_day', 'id_user', ' is_active'];

    public static function getCreditCardById($id) 
    {
        $month = Request::get('month');
        $month = $month + 1;
        $year = date('Y');

        $creditCard = self::where('id_user',  auth('api')->user()->id)
        ->where('id',  $id)
        ->first();

        if (!$creditCard) {
            return false;
        }

        $expenseCreditCard = self::join('expense_credit_card', 'credit_card.id', '=', 'expense_credit_card.id_credit_card')
            ->join('transaction', 'transaction.id', '=', 'expense_credit_card.id_transaction')
            ->join('transaction_item', 'transaction.id', '=', 'transaction_item.id_transaction')
            ->join('category', 'category.id', '=', 'transaction.id_category')
            ->select(
                'transaction.description', 
                'transaction.id_user', 
                'transaction.is_income', 
                'transaction.name', 
                'transaction.id_category', 
                'transaction_item.value',
                'transaction_item.created_at', 
                'transaction_item.due_date', 
                'expense_credit_card.id_transaction', 
                'category.icon'
            )
            ->where('credit_card.id_user', auth('api')->user()->id)
            ->whereRaw("MONTH(transaction_item.due_date) = {$month}")
            ->whereRaw("YEAR(transaction_item.due_date) = {$year}")
            ->where('credit_card.id', $creditCard->id)
            ->orderBy("transaction_item.id", "desc")
            ->get();
        
        $total = 0;
        foreach ($expenseCreditCard as $key => $value) {
            $total += $value->value;
        }

        $creditCard->items = $expenseCreditCard;
        $creditCard->total = $total;

        return $creditCard;
    }

    public static function getCreditCards() 
    {
        $creditCards = self::where('id_user', auth('api')->user()->id)
        ->where('is_active', true)
        ->get();

        if (!$creditCards) {
            return false;
        }

        $ar =  [];
        foreach ($creditCards as $key => $card) {
            $expenseCreditCard = self::join('expense_credit_card', 'credit_card.id', '=', 'expense_credit_card.id_credit_card')
                ->selectRaw('sum(transaction.value) as total')
                ->join('transaction', 'transaction.id', '=', 'expense_credit_card.id_transaction')
                ->join('category', 'category.id', '=', 'transaction.id_category')
                ->where('credit_card.id_user', auth('api')->user()->id)
                ->where('credit_card.id', $card->id)
                ->first();

            $card->total = $expenseCreditCard->total;
            $ar[] = $card;
        }
        if (!$ar) {
            return $creditCards;
        }

        return $ar;
    }
}
