<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class Arquivo extends Model
{
    protected $table = 'sistema.arquivo';
    protected $primaryKey = 'id_arquivo';
    protected $fillable = ['id_arquivo', 'mm_caminho', 'no_arquivo', 'qt_tamanho', 'tp_arquivo', 'bo_temp', 'no_arquivo_miniatura', 'mm_caminho_miniatura'];

    public static function saveFile($file, $folder)
    {
        return self::create(self::upload($file, $folder));
    }

    public static function upload($file, $folder)
    {
        if (null == $folder) {
            $folder = 'default';
        }
        $path = $file->store($folder);
        $miniatura = self::crop($file, $path);
        if (!$path) {
            return false;
        }
        $dados['mm_caminho_miniatura'] = $miniatura;
        $dados['mm_caminho'] = 'public/storage/'.$path;
        $dados['no_arquivo_miniatura'] = basename($miniatura);
        $dados['no_arquivo'] = basename($path);
        $dados['qt_tamanho'] = $file->getSize();
        $dados['tp_arquivo'] = $file->getClientOriginalExtension();

        return $dados;
    }

    public static function updateFile($file, $id, $folder)
    {
        $arquivo = self::find($id);

        if (!$arquivo) {
            return response(['response' => 'Arquivo Não encontrado'], 400);
        }

        $dados = self::upload($file, $folder);
        $arquivo = Helpers::processarColunasUpdate($arquivo, $dados);

        if (!$arquivo->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public static function deleteFileById($id_arquivo)
    {
        return self::where('id_arquivo', $id_arquivo)->delete();
    }

    public static function formataIsImage($request)
    {
        $type = current(explode('/', $request->getMimeType()));
        if ('image' != $type) {
            return false;
        }

        return true;
    }

    private static function crop($file, $path)
    {
        $type = current(explode('/', $file->getMimeType()));
        if ('image' != $type) {
            return false;
        }
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $path = str_replace(".{$extension}", '', $path);
        $name = storage_path().'/app/public/'.$path.'_min.'.$extension;

        if (Image::make($file)->fit(200, 200)->save($name)) {
            return 'public/storage/'.$path.'_min.'.$extension;
        }

        return false;
    }
}
