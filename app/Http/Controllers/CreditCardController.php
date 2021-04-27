<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class CreditCardController extends Controller
{
    public function index()
    {
        $creditCard = \App\CreditCard::where('id_user', auth('api')->user()->id)->get();

        if (!$creditCard) {
            return response(['response' => 'Você ainda não tem cartões cadastrados.'], 400);
        }

        return response($creditCard);
    }

    public function store(Request $request)
    {
        $request['id_user'] = auth('api')->user()->id;

        $creditCard = \App\CreditCard::create($request->all());

        if (!$creditCard) {
            return response(['message' => 'Erro ao salvar'], 400);
        }

        return response($creditCard);
    }

    public function show($id)
    {
        $creditCard = \App\CreditCard::find($id);

        if (!$creditCard) {
            return response(['response' => 'Erro!'], 400);
        }

        return response($creditCard);
    }

    public function update(Request $request, $id)
    {
        $creditCard = \App\CreditCard::find($id);

        if ($creditCard) {
            if ($creditCard['id_user'] != auth('api')->user()->id) {
                return response(['error' => 'Sem permissão'], 400);
            }

            $creditCard = Helpers::processarColunasUpdate($creditCard, $request->all());

            if (!$creditCard->save()) {
                return response(['response' => 'Erro ao alterar cartão de crédito'], 400);
            }

            return response($creditCard);
        }

        return response(['response' => 'Cartão de crédito não encontrado']);
    }

    public function destroy($id)
    {
        $creditCard = \App\CreditCard::find($id);

        if (!$creditCard) {
            return response(['response' => 'Cartão de crédito não encontrado'], 400);
        }

        if ($creditCard['id_user'] != auth('api')->user()->id) {
            return response(['error' => 'Sem permissão'], 400);
        }

        $creditCard->is_active = false;

        if (!$creditCard->save()) {
            return response(['response' => 'Erro ao deletar cartão de crédito'], 400);
        }

        return response(['response' => 'Cartão de crédito inativado com sucesso']);
    }
}
