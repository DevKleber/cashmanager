<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;

class Category extends Model
{
    protected $table = 'category';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'id_category_parent', 'name', 'is_active', 'icon', 'is_income', 'id_user'];

    public static function saveCategoryAutomatically($id)
    {
        $json = Storage::disk('local')->get('/json/categories.json');
        $json = json_decode($json, true);

        foreach ($json['dados']['pais'] as $key => $value) {
            $request['name'] = $value['no_categoria'];
            $request['id_category_parent'] = null;
            $request['id_user'] = $id;
            $request['icon'] = $value['icon'];
            $request['is_income'] = false;
            $categories = self::create($request);
            if (!$categories) {
                return false;
            }

            foreach ($value['filhas'] as $key => $filhas) {
                $request['name'] = $filhas['no_categoria'];
                $request['id_category_parent'] = $categories->id;
                $request['id_user'] = $id;
                $request['icon'] = $filhas['icon'];
                $request['is_income'] = false;
                $subCategories = self::create($request);
                if (!$subCategories) {
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

    public static function getCategories() {
        $type = Request::get('type');

        $query = \App\Category::where('id_user', auth('api')->user()->id);
        
        if ($type == "income") {
            $query->where('is_income', true);
        }

        if ($type == "expense") {
            $query->where('is_income', false);
        }
        
        return $query->get();
    }
}
