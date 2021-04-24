<?php

namespace App\Http\Controllers;

use Helpers;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menu = \App\Menu::menu();
        if (!$menu) {
            return response(['response' => 'Não existe menu'], 400);
        }

        return response(['dados' => $menu]);
    }

    public function store(Request $request)
    {
        $menu = \App\Menu::create($request->all());
        if (!$menu) {
            return  response(['response' => 'Erro ao salvar Menu'], 400);
        }

        return response(['response' => 'Salvo com sucesso', 'dados' => $menu]);
    }

    public function show($id)
    {
        $menu = \App\Menu::find($id);
        if (!$menu) {
            return response(['response' => 'Não existe Menu'], 400);
        }

        return response($menu);
    }

    public function update(Request $request, $id)
    {
        $menu = \App\Menu::find($id);

        if (!$menu) {
            return response(['response' => 'Menu Não encontrado'], 400);
        }
        $menu = Helpers::processarColunasUpdate($menu, $request->all());

        if (!$menu->update()) {
            return response(['response' => 'Erro ao alterar'], 400);
        }

        return response(['response' => 'Atualizado com sucesso']);
    }

    public function destroy($id)
    {
        $menu = \App\Menu::find($id);

        if (!$menu) {
            return response(['response' => 'Menu Não encontrado'], 400);
        }
        $menu->bo_ativo = false;
        if (!$menu->save()) {
            return response(['response' => 'Erro ao deletar Menu'], 400);
        }

        return response(['response' => 'Menu Inativado com sucesso']);
    }
}
