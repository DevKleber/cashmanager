<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionAccount extends Model
{
    protected $table = 'transaction_account';
    protected $primaryKey = 'transaction_id';
    protected $fillable = ['account_id', 'transaction_id'];
}