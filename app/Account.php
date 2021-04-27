<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Account extends Model
{
    protected $table = 'account';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'description', 'id_banking', 'current_balance', 'id_user'];
}