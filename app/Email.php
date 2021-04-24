<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mail;
use App\Mail\RecoverPassword;
use App\Mail\NewUser;

class Email extends Model
{
    protected $table = 'pessoa.email';
    protected $primaryKey = 'id_email';
    protected $fillable = ['id_email', 'id_tipoemail', 'ee_email', 'created_at', 'updated_at'];

    public static function sendEmailNewCount($user)
    {
        $usuario = $user->toArray();
        Mail::to($usuario['ee_email'])->queue(new RecoverPassword($usuario));
    }

    public static function sendEmailNewUser($user)
    {
        $usuario = $user->toArray();
        Mail::to(current($usuario['emails'])['ee_email'])->queue(new NewUser($usuario));

    }
}
