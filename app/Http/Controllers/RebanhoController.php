<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class RebanhoController extends Controller
{
    public function index()
    {
        $rebanho = \App\Rebanho::all();
        if (!$rebanho) {
            return response(['response' => 'Não existe Rebanho'], 400);
        }

        return response(['dados' => $rebanho]);
    }

    public function store(Request $request)
    {
        $rebanho = \App\Rebanho::create($request->all());
        if (!$rebanho) {
            return response(['response' => 'Erro ao salvar Rebanho'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $rebanho]);
    }

    public function show($id)
    {
        $rebanho = \App\Rebanho::find($id);
        if (!$rebanho) {
            return response(['response' => 'Não existe Rebanho'], 400);
        }

        $files = \App\RebanhoArquivo::where('id_rebanho', $id)
            ->join('sistema.arquivo as a', 'a.id_arquivo', '=', 'rebanho.rebanho_arquivo.id_arquivo')
            ->where('bo_ativo', true)
            ->get()
        ;

        $rebanho['files'] = $files;

        return response($rebanho);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only(['id_raca', 'nu_identificacao', 'no_identificacao', 'dt_nascimento']);
        $data['dt_nascimento'] = Helpers::convertdateBr2DB($data['dt_nascimento'] ?? '');
        $rebanho = \App\Rebanho::find($id);

        if (!$rebanho) {
            return response(['response' => 'Animal Não encontrado'], 400);
        }
        $rebanho = Helpers::processarColunasUpdate($rebanho, $data);

        if (!$rebanho->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        try {
            $file = $this->updateFile($request, $id);
        } catch (\Throwable $th) {
            return response(['response' => $th->getMessage()], 400);
        }

        return response(['response' => 'Atualizado com sucesso!', 'dados' => $rebanho, 'file' => $file]);
    }

    public function destroy($id)
    {
        $rebanho = \App\Rebanho::find($id);

        if (!$rebanho) {
            return response(['response' => 'Rebanho Não encontrado'], 400);
        }
        $rebanho->bo_ativo = false;
        if (!$rebanho->save()) {
            return response(['response' => 'Erro ao deletar Rebanho'], 400);
        }

        return response(['response' => 'Rebanho Inativado com sucesso']);
    }

    private function updateFile($request, $id_rebanho)
    {
        if ($request->hasFile('file')) {
            $updateToFalse = \App\RebanhoArquivo::where('id_rebanho', $id_rebanho)->update(['bo_ativo' => false]);

            foreach ($request->file('file') as $key => $value) {
                if (!\App\Arquivo::formataIsImage($value)) {
                    throw new \Exception('Formato inválido');
                }

                try {
                    $upload = \App\Arquivo::saveFile($value, 'rebanho');
                } catch (\Throwable $th) {
                    throw new \Exception($th->getMessage());
                }

                if (!$upload) {
                    $resp['errors'][] = $value->getClientOriginalName();

                    continue;
                }

                $rebanho['id_arquivo'] = $upload->id_arquivo;
                $rebanho['id_rebanho'] = $id_rebanho;
                $rebanhoArquivo = \App\RebanhoArquivo::create($rebanho);

                if (!$rebanhoArquivo) {
                    $resp['errors'][] = $value->getClientOriginalName();

                    continue;
                }

                $resp['success'][$upload->id_arquivo]['file'] = $value->getClientOriginalName();
                $resp['success'][$upload->id_arquivo]['rebanho'] = $rebanhoArquivo;
                $resp['success'][$upload->id_arquivo]['upload'] = $upload;
            }

            return $resp;
        }
    }
}
