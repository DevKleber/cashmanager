<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function test()
    {
        $category = \App\Category::get();

        if (!$category) {
            return response(['response' => 'Categoria não encontrada'], 400);
        }

        $tree = \App\Category::buildTree($category);

        return response($tree);
    }
    public function index()
    {
        $category = \App\Category::where('id_user', auth('api')->user()->id)->get();

        if (!$category) {
            return response(['response' => 'Categoria não encontrada'], 400);
        }

        $tree = \App\Category::buildTree($category);

        return response($tree);
    }

    public function store(Request $request)
    {
        $ar = $request->all();
        $ar['id_user'] = auth('api')->user()->id;

        $category = \App\Category::create($ar);

        if (!$category) {
            return  response(['message' => 'Erro ao salvar categoria'], 400);
        }

        return response($category);
    }

    public function show($id)
    {
        $category = \App\Category::find($id);

        if ($category->id_user != auth('api')->user()->id) {
            return response(['error' => 'Não tem permissão para acessar essa categoria'], 400);
        }

        if (!$category) {
            return response(['response' => 'Não existe categoria'], 400);
        }

        return response($category);
    }

    public function update(Request $request, $id)
    {
        $category = \App\Category::find($id);


        if ($category) {
            if ($category['id_user'] != auth('api')->user()->id) {
                return response(['error' => 'Não tem permissão para alterar esse categoria'], 400);
            }

            if ($category->id == $request->id_category_parent) {
                return response(['error' => 'Parentesco da categoria é invalido'], 400);
            }

            $category = Helpers::processarColunasUpdate($category, $request->all());

            if (!$category->save()) {
                return response(['response' => 'categoria não foi atualizado'], 400);
            }

            return response($category);
        }

        return response(['response' => 'categoria não encontrado']);
    }

    public function destroy($id)
    {
        $category = \App\Category::find($id);

        if (!$category) {
            return response(['response' => 'categoria Não encontrado'], 400);
        }

        $category->is_active = false;

        if (!$category->save()) {
            return response(['response' => 'Erro ao deletar categoria'], 400);
        }

        return response(['response' => 'categoria Inativado com sucesso']);
    }
}
