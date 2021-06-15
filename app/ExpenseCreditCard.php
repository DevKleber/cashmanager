<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
class ExpenseCreditCard extends Model
{
    protected $table = 'expense_credit_card';
    protected $primaryKey = 'id_transaction';
    protected $fillable = ['id_transaction', 'id_credit_card'];

}
