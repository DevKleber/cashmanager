<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class MovimentacaoTipoArquivoController extends Controller
{
    public function index()
    {
        $movimentacaoTipoArquivo = \App\MovimentacaoTipoArquivo::getAllTypeFiles();
        if (!$movimentacaoTipoArquivo) {
            return response(['response' => 'Não existe movimentacaoTipoArquivo'], 400);
        }

        return response(['dados' => $movimentacaoTipoArquivo]);
    }

    public function store(Request $request)
    {
        $request['bo_ativo'] = true;

        $movimentacaoTipoArquivo = \App\MovimentacaoTipoArquivo::create($request->all());
        if (!$movimentacaoTipoArquivo) {
            return  response(['response' => 'Erro ao salvar MovimentacaoTipoArquivo'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $movimentacaoTipoArquivo]);
    }

    public function show($id)
    {
        $movimentacaoTipoArquivo = \App\MovimentacaoTipoArquivo::find($id);
        if (!$movimentacaoTipoArquivo) {
            return response(['response' => 'Não existe MovimentacaoTipoArquivo'], 400);
        }

        return response($movimentacaoTipoArquivo);
    }

    public function update(Request $request, $id)
    {
        $movimentacaoTipoArquivo = \App\MovimentacaoTipoArquivo::find($id);

        if (!$movimentacaoTipoArquivo) {
            return response(['response' => 'MovimentacaoTipoArquivo Não encontrado'], 400);
        }
        $movimentacaoTipoArquivo = Helpers::processarColunasUpdate($movimentacaoTipoArquivo, $request->all());

        if (!$movimentacaoTipoArquivo->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $movimentacaoTipoArquivo = \App\MovimentacaoTipoArquivo::find($id);

        if (!$movimentacaoTipoArquivo) {
            return response(['response' => 'MovimentacaoTipoArquivo Não encontrado'], 400);
        }
        $movimentacaoTipoArquivo->bo_ativo = false;
        if (!$movimentacaoTipoArquivo->save()) {
            return response(['response' => 'Erro ao deletar MovimentacaoTipoArquivo'], 400);
        }

        return response(['response' => 'Tipo de arquivo inativado com sucesso']);
    }
}
