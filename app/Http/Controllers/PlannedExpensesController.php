<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlannedExpensesController extends Controller
{
    public function index()
    {
        $plannedExpenses = \App\Category::leftJoin('planned_expenses as pe', 'pe.id_category', '=', 'category.id')
            ->where('id_user', auth('api')->user()->id)
            ->where('is_active', true)
            ->where('is_income', false)
            ->select('category.id', 'category.id_category_parent', 'category.name', 'category.icon', 'pe.value_percent')
            ->get()
        ;

        if (!$plannedExpenses) {
            return response(['response' => 'Erro ao obter os planejamentos!'], 400);
        }

        return response(\App\Category::buildTree($plannedExpenses));
    }

    public function store(Request $request)
    {
        \DB::beginTransaction();

        $plannedExpenses = \App\PlannedExpenses::join('category as c', 'planned_expenses.id_category', '=', 'c.id')
            ->where('id_user', auth('api')->user()->id)
        ;
        $plannedExpenses->delete();

        if (!\App\PlannedExpenses::validateCategoriesToPlannedExpenses($request->all())) {
            \DB::rollBack();

            return response(['response' => 'Sem permissão!'], 400);
        }

        foreach ($request->all() as $category) {
            $plannedExpenseSaved = \App\PlannedExpenses::create($category);
            if (!$plannedExpenseSaved) {
                \DB::rollBack();

                return response(['response' => 'Erro ao salvar planejamento!'], 400);
            }
        }

        \DB::commit();

        return response(['response' => 'Salvo com sucesso!']);
    }
}
