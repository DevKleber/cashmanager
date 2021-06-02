<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\Transaction;

class TransactionItem extends Model
{
    protected $table = 'transaction_item';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'is_paid', 'due_date', 'value', 'currenct_installment', 'installment', 'id_transaction'];

    public static function getItensByIdTransaction(int $id)
    {
        return self::where('id_transaction', $id)->get();
    }

    public static function saveItens(array $ar, Transaction $transaction)
    {
        $parceledValue = $transaction->value;
        $oneDay = 1;
        $dayCloseCard = false;
        
        if (!$ar['is_income'] &&  $ar['installment']) {
            $parceledValue = $transaction->value / $ar['installment'];

            if ($ar['id_creditcard']) {
                $creditCard = \App\CreditCard::find($ar['id_creditcard']);

                if ($creditCard) {
                    $dia = (int) date("d");
                    if ((int) date("d") <= (int) $creditCard->closing_day) {
                        $dayCloseCard = true;
                        
                    }
                }
                
            }
        }


        if (!$ar['installment']) {
            $oneDay = 0;
            $ar['installment'] = 1;
        }
        for ($i=0; $i < $ar['installment']; $i++) {
            $item = [];
            $item['id_transaction'] = $transaction->id;
            $item['value'] = $parceledValue;
            $item['currenct_installment'] = ($i + 1);
            $item['installment'] = $ar['installment'];
            $item['is_paid'] = $ar['is_paid'];
            $item['due_date'] = self::formatDueDate($ar, ($i + $oneDay), $dayCloseCard);
            $id = self::create($item);

            if (!$id) {
                return false;
            }
        }
        return true;
    }

    public static function formatDueDate($ar, $mountIncrement, $dayCloseCard)
    {
        $due_date = $ar['due_date'];
        $is_paid = $ar['is_paid'];

        if (!$due_date) {
            return null;
        }

        
        $due_date = new \DateTime($due_date);
        
        if ($is_paid) {
            return $due_date;
        }

        if ($dayCloseCard) {
            $mountIncrement = $mountIncrement - 1;
        }

        // if ($mountIncrement) {
        $due_date = $due_date->modify("+ {$mountIncrement} month");
        // }

        return $due_date;
    }
}
