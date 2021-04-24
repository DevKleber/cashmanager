<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class MovimentacaoArquivoController extends Controller
{
    public function index($id)
    {
        $movimentacaoArquivo = \App\MovimentacaoArquivo::join('sistema.arquivo', 'sistema.arquivo.id_arquivo', '=', 'rebanho.movimentacao_arquivo.id_arquivo')
            ->where('bo_ativo', true)
            ->where('id_movimentacao', $id)
            ->get()
        ;

        if (!$movimentacaoArquivo) {
            return response(['response' => 'Não existe arquivos para essa movimentação'], 400);
        }

        return response(['dados' => $movimentacaoArquivo]);
    }

    public function store(Request $request)
    {
        $resp['errors'] = null;
        $resp['success'] = null;
        $movimentacao['id_movimentacao'] = $request['id_movimentacao'];

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $key => $value) {
                // if (!\App\Arquivo::formataIsImage($value)) {
                //     return response(['response' => 'Formato inválido'], 400);
                // }
                \DB::beginTransaction();

                try {
                    $upload = \App\Arquivo::saveFile($value, 'movimentacao');
                } catch (\Throwable $th) {
                    return response(['response' => $th->getMessage(), 400]);
                }

                if (!$upload) {
                    $resp['errors'][] = $value->getClientOriginalName();
                    \DB::rollBack();

                    continue;
                }

                $movimentacao['id_arquivo'] = $upload->id_arquivo;
                $movimentacao['id_movimentacaotipoarquivo'] = $request['id_movimentacaotipoarquivo'];
                $movimentacaoArquivo = \App\MovimentacaoArquivo::create($movimentacao);

                if (!$movimentacaoArquivo) {
                    $resp['errors'][] = $value->getClientOriginalName();
                    \DB::rollBack();

                    continue;
                }

                $resp['success'][$upload->id_arquivo]['file'] = $value->getClientOriginalName();
                $resp['success'][$upload->id_arquivo]['movimentacao'] = $movimentacaoArquivo;
                $resp['success'][$upload->id_arquivo]['upload'] = $upload;
                \DB::commit();
            }

            return response($resp);
        }
    }

    public function show($id)
    {
        $movimentacaoArquivo = \App\MovimentacaoArquivo::find($id);
        if (!$movimentacaoArquivo) {
            return response(['response' => 'Não existe MovimentacaoArquivo'], 400);
        }

        return response($movimentacaoArquivo);
    }

    public function update(Request $request, $id)
    {
        $movimentacaoArquivo = \App\MovimentacaoArquivo::find($id);

        if (!$movimentacaoArquivo) {
            return response(['response' => 'MovimentacaoArquivo Não encontrado'], 400);
        }
        $movimentacaoArquivo = Helpers::processarColunasUpdate($movimentacaoArquivo, $request->all());

        if (!$movimentacaoArquivo->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $movimentacaoArquivo = \App\MovimentacaoArquivo::find($id);

        if (!$movimentacaoArquivo) {
            return response(['response' => 'MovimentacaoArquivo Não encontrado'], 400);
        }
        $movimentacaoArquivo->bo_ativo = false;
        if (!$movimentacaoArquivo->save()) {
            return response(['response' => 'Erro ao deletar MovimentacaoArquivo'], 400);
        }

        return response(['response' => 'Arquivo deletado com sucesso']);
    }
}
