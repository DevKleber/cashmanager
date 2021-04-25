<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $table = 'category';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'id_category_parent', 'name', 'is_active', 'created_at', 'updated_at', 'icon', 'is_income', 'id_user'];

    public static function categoriaDespesas($id_usuario)
    {
        $json = Storage::disk('local')->get('/json/categories.json');
        $json = json_decode($json, true);

        foreach ($json['dados']['pais'] as $key => $value) {
            $request['name'] = $value['no_categoria'];
            $request['id_category_parent'] = null;
            $request['id_user'] = $id_usuario;
            $request['icon'] = $value['icon'];
            $categoriaDespesa = self::create($request);
            if (!$categoriaDespesa) {
                return false;
            }

            foreach ($value['filhas'] as $key => $filhas) {
                $request['name'] = $filhas['no_categoria'];
                $request['id_category_parent'] = $categoriaDespesa->id_categoria_despesa;
                $request['id_user'] = $id_usuario;
                $request['icon'] = $filhas['icon'];
                $categoriaDespesaFilhas = self::create($request);
                if (!$categoriaDespesaFilhas) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function buildTree($elements, $parentId = 0)
    {
        $branch = [];
        foreach ($elements as $key => $element) {
            if ($element->id_category_parent == $parentId) {
                $children = self::buildTree($elements, $element->id);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
