<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    protected $table = 'credit_card';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'closing_day', 'due_day', 'id_user', ' is_active'];
}
