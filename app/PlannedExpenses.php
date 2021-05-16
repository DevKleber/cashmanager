<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlannedExpenses extends Model
{
    protected $table = 'planned_expenses';
    protected $primaryKey = 'id_category';

    protected $fillable = ['id_category', 'value_percent'];

    public static function validateCategoriesToPlannedExpenses($request)
    {
        $idCategories = [];
        foreach ($request as $category) {
            $idCategories[] = $category['id_category'];
        }
        $categories = \App\Category::whereIn('id', $idCategories)
            ->where('id_user', auth('api')->user()->id)
        ;
        if (count($idCategories) !== $categories->count()) {
            return false;
        }

        return true;
    }
}
