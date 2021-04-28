<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseAccount extends Model
{
    protected $table = 'expense_account';
    protected $primaryKey = 'transaction_id';
    protected $fillable = ['account_id', 'transaction_id'];
}