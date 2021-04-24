<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class PropriedadeGaleriaController extends Controller
{
    public function index($id)
    {
        $propriedadeGaleria = \App\PropriedadeGaleria::join('sistema.arquivo', 'sistema.arquivo.id_arquivo', '=', 'propriedade.galeria.id_arquivo')->where('bo_ativo', true)->where('id_propriedade', $id)->orderBy('id_galeria', 'desc')->get();

        if (!$propriedadeGaleria) {
            return response(['response' => 'Não existe imagem'], 400);
        }

        return response(['dados' => $propriedadeGaleria]);
    }

    public function store(Request $request, $id_propriedade)
    {
        $resp['errors'] = null;
        $resp['success'] = null;
        if ($request->hasFile('file')) {
            $galary['id_propriedade'] = $id_propriedade;
            foreach ($request->file('file') as $key => $value) {
                if (!\App\Arquivo::formataIsImage($value)) {
                    return response(['response' => 'Formato inválido'], 400);
                }
                \DB::beginTransaction();

                $upload = \App\Arquivo::saveFile($value, 'galary');

                if (!$upload) {
                    $resp['errors'][] = $value->getClientOriginalName();
                    \DB::rollBack();

                    continue;
                }

                $galary['id_arquivo'] = $upload->id_arquivo;
                $propriedadeGaleria = \App\PropriedadeGaleria::create($galary);

                if (!$propriedadeGaleria) {
                    $resp['errors'][] = $value->getClientOriginalName();
                    \DB::rollBack();

                    continue;
                }

                $resp['success'][$upload->id_arquivo]['file'] = $value->getClientOriginalName();
                $resp['success'][$upload->id_arquivo]['galary'] = $propriedadeGaleria;
                $resp['success'][$upload->id_arquivo]['upload'] = $upload;
                \DB::commit();
            }

            return response($resp);
        }
    }

    public function show($id)
    {
        $propriedadeGaleria = \App\PropriedadeGaleria::find($id);
        if (!$propriedadeGaleria) {
            return response(['response' => 'Não existe PropriedadeGaleria'], 400);
        }

        return response($propriedadeGaleria);
    }

    public function update(Request $request, $id)
    {
        $propriedadeGaleria = \App\PropriedadeGaleria::find($id);

        if (!$propriedadeGaleria) {
            return response(['response' => 'PropriedadeGaleria Não encontrado'], 400);
        }
        $propriedadeGaleria = Helpers::processarColunasUpdate($propriedadeGaleria, $request->all());

        if (!$propriedadeGaleria->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $propriedadeGaleria = \App\PropriedadeGaleria::find($id);

        if (!$propriedadeGaleria) {
            return response(['response' => 'Imagem não encontrado'], 400);
        }
        $propriedadeGaleria->bo_ativo = false;
        if (!$propriedadeGaleria->save()) {
            return response(['response' => 'Erro ao deletar imagem'], 400);
        }

        return response(['response' => 'imagem deletada com sucesso']);
    }
}
