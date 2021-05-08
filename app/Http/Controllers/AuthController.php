<?php

namespace App\Http\Controllers;

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
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only(['email', 'password']);
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function newAccount(Request $request)
    {
        if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
            return response(['response' => 'E-mail inválido'], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        \DB::beginTransaction();

        $accountAlreadyExists = \App\User::where('email', $request['email'])->first();
        if ($accountAlreadyExists) {
            return response(['response' => 'Dados indisponíveis'], 400);
        }

        $firstName = current(explode(' ', $request['name']));
        $firstNameToUpperCase = strtoupper($firstName);

        if ($firstNameToUpperCase == strtoupper($request['password'])) {
            return response(['response' => 'Sua senha precisa ser diferente do seu nome'], 400);
        }

        if (\App\User::passwordIsWeak($request['password'])) {
            return response(['response' => 'Senha informada é muito fraca'], 400);
        }
        $currentPassword = $request['password'];
        $request['password'] = bcrypt($request['password']);
        $user = \App\User::create($request->all());

        $saveCategories = \App\Category::saveCategoryAutomatically($user->id);
        if (!$saveCategories) {
            \DB::rollback();

            return response(['response' => 'Erro ao criar categorias. Entre em contato'], 400);
        }
        \DB::commit();
        $request['password'] = $currentPassword;
        return $this->login($request);
    }

    public function me()
    {
        return \App\User::find(auth('api')->user()->id);
    }

    public function changePassword(Request $request)
    {
        $id = auth('api')->user()->id;
        $dados = $request->only(['currentPassword', 'newPassword', 'confirmPassword']);
        $user = \App\User::find($id);
        $nomeEmployee = strtolower(explode(' ', $user->name));

        if (!$id) {
            return response(['error' => 'Unauthorized'], 401);
        }

        if ($dados['newPassword'] != $dados['confirmPassword']) {
            return response(['response' => 'As senhas não conferem'], 400);
        }

        if (\App\User::passwordIsWeak($dados['newPassword'])) {
            return response(['response' => 'Senha informada é muito fraca'], 400);
        }

        if ($nomeEmployee == $dados['newPassword']) {
            return response(['response' => 'Senha tem que ser diferente do nome'], 400);
        }

        return \App\User::changePassword($dados, $id);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

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
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        return \App\User::recoverPassword($request->all());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'me' => auth('api')->user(),
        ]);
    }
}
