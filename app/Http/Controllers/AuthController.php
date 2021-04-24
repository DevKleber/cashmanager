<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only(['login', 'password']);
        $credentials['login'] = Helpers::removerCaracteresEspeciaisEspacos($credentials['login']);
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    // @webipe@

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return  \App\Funcionario::getEmployee(auth('api')->user()->id_pessoa);
    }

    public function changePassword(Request $request)
    {
        $id_pessoa = auth('api')->user()->id_pessoa;
        $dados = $request->only(['currentPassword', 'newPassword', 'confirmPassword']);
        $employee = \App\Funcionario::getEmployee($id_pessoa);
        $nomeEmployee = strtolower(current(explode(' ', $employee['employee']->no_pessoa)));

        if (!$id_pessoa) {
            return response(['error' => 'Unauthorized'], 401);
        }

        if ($dados['newPassword'] != $dados['confirmPassword']) {
            return response(['response' => 'As senhas não conferem'], 400);
        }

        if (in_array($dados['newPassword'], \App\User::getWorstPassword())) {
            return response(['response' => 'A senha informada é muito fraca'], 400);
        }

        if ($nomeEmployee == $dados['newPassword']) {
            return response(['response' => 'Senha tem que ser diferente do nome'], 400);
        }

        return  \App\User::changePassword($dados, $id_pessoa);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }
    public function tokenIsValidate()
    {
        if (!auth()->validate()) {
            return response(['error' => 'Token is Invalid'], 400);
        }

    }



    public function recoverPassword(Request $request)
    {
        return \App\User::recoverPassword($request->all());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $me = $this->me();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'me' => $me['employee'],
        ]);
    }
}
