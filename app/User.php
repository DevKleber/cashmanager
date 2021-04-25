<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = 'user';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'password', 'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function recoverPassword($request)
    {
        $userAr = self::where('email', $request['email'])->first();
        $user = self::find($userAr['id']);

        if (!$user) {
            return response(['error' => 'Dados inválidos'], 400);
        }

        $password = Str::random(8);
        $user->password = \Hash::make(($password));

        // TODO: faltando criar as tabelas de job
        // \App\Email::sendEmailNewCount($user);

        if (!$user->save()) {
            return response(['response' => 'categoria não foi atualizado'], 400);
        }

        return response($user);
    }

    public static function changePassword($request, $id_pessoa)
    {
        $funcionario = \App\Funcionario::find($id_pessoa);

        if (!\Hash::check($request['currentPassword'], $funcionario->password)) {
            return response(['response' => 'Senha incorreta'], 400);
        }

        $funcionario->password = \Hash::make(($request['newPassword']));
        $funcionario->bo_mudar_senha = false;
        if (!$funcionario->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public static function getWorstPassword()
    {
        return [
            '123456',
            '123456789',
            '123abc',
            'qwerty',
            'password',
            '111111',
            '12345678',
            'abc123',
            '1234567',
            'password1',
            '12345',
            '1234567890',
            '123123',
            '000000',
            'iloveyou',
            '1234',
            '1q2w3e4r5t',
            'qwertyuiop',
            '123',
            'monkey',
            'dragon',
        ];
    }

    public static function passwordIsWeak($password)
    {
        $pass = array_flip(self::getWorstPassword());
        if (isset($pass[$password])) {
            return true;
        }

        return false;
    }
}
