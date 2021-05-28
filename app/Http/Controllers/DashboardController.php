<?php

namespace App\Http\Controllers;

use Helpers;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = date('m');
        $totalEntradas = $this->totalEntradas($currentMonth);
        $totalSaida = $this->totalSaida($currentMonth);
        $planejamento = $this->planejamentoSummary($currentMonth);
        $totalPlanejamento = $this->calculoPlanejamento($planejamento);
        $entradasDoAno = $this->graficoEntradasDoAno();
        $saidasDoAno = $this->graficoSaidasDoAno();
        $categoriasDoAno = $this->categoriasDoAno();

        return [
            'totalEntradas' => $totalEntradas,
            'totalSaida' => $totalSaida,
            'totalPlanejamento' => $totalPlanejamento,
            'planejamento' => $planejamento,
            'entradasDoAno' => $entradasDoAno,
            'saidasDoAno' => $saidasDoAno,
            'categoriasDoAno' => $categoriasDoAno,
        ];
    }

    private function planejamentoSummary($mes, $ano = null)
    {
        $ano = null === $ano ? date('Y') : $ano;

        return \App\TransactionItem::join('transaction as t', 't.id', '=', 'transaction_item.id_transaction')
            ->join('category as c', 'c.id', '=', 't.id_category')
            ->join('category as cp', 'cp.id', '=', 'c.id_category_parent')
            ->leftJoin('planned_expenses as pe', 'pe.id_category', '=', 'cp.id')
            ->whereRaw("MONTH(due_date) = {$mes} AND YEAR(due_date) = {$ano} and t.is_income = false")
            ->where('t.id_user', auth('api')->user()->id)
            ->groupByRaw('cp.id, cp.name, pe.value_percent')
            ->selectRaw('
                cp.id as id_category,
				cp.name,
				sum(transaction_item.value) as total,
				pe.value_percent,
				cp.icon,
				(SELECT sum(ti.value)
					FROM transaction_item ti
					JOIN `transaction` t ON t.id = ti.id_transaction
					WHERE t.is_income = TRUE
					AND YEAR(due_date) = '.$ano.'
					AND MONTH(due_date) = '.$mes.'
				) AS income
			')
            ->get()
        ;
    }

    private function calculoPlanejamento($planejamento)
    {
        $totalPlanejado = \App\PlannedExpenses::join('category as c', 'c.id', '=', 'planned_expenses.id_category')
            ->where('id_user', auth('api')->user()->id)
            ->selectRaw('sum(value_percent) as planejado')->first();

        $sumTotalGasto = 0;
        $income = 0;
        $total = 0;

        if (isset($planejamento[0])) {
            $income = (float) $planejamento[0]->income;
        }

        foreach ($planejamento as $value) {
            $sumTotalGasto += $value->total;
        }

        if ($sumTotalGasto) {
            $total = $sumTotalGasto * 100 / $income;
        }


        return ['totalPlanejado' => (float) $totalPlanejado->planejado, 'total'=>$total];
    }

    private function totalEntradas($mes, $ano = null)
    {
        $ano = null === $ano ? date('Y') : $ano;

        return \App\TransactionItem::join('transaction as t', 't.id', '=', 'transaction_item.id_transaction')
            ->whereRaw("MONTH(due_date) = {$mes} AND YEAR(due_date) = {$ano} and t.is_income = true")
            ->where('id_user', auth('api')->user()->id)
            ->selectRaw('COALESCE(sum(transaction_item.value),0) as total')
            ->first()
        ;
    }

    private function totalSaida($mes, $ano = null)
    {
        $ano = null === $ano ? date('Y') : $ano;

        return \App\TransactionItem::join('transaction as t', 't.id', '=', 'transaction_item.id_transaction')
            ->whereRaw("MONTH(due_date) = {$mes} AND YEAR(due_date) = {$ano} and t.is_income = false")
            ->where('id_user', auth('api')->user()->id)
            ->selectRaw('COALESCE(sum(transaction_item.value),0) as total')
            ->first()
        ;
    }

    private function categoriasDoAno()
    {
        $ano = date('Y');
        $rgbRandom = Helpers::getColor();
        $rgb = "rgba({$rgbRandom},1)";

        return \App\TransactionItem::join('transaction as t', 't.id', '=', 'transaction_item.id_transaction')
            ->join('category as c', 'c.id', '=', 't.id_category')
            ->whereRaw("YEAR(due_date) = {$ano} and t.is_income = false")
            ->where('t.id_user', auth('api')->user()->id)
            ->selectRaw('c.name, COALESCE(sum(transaction_item.value),0) as total,"#7F7F7F" as legendFontColor, "'.$rgb.'" as color')
            ->groupByRaw('t.id_category, c.name ')
            ->get()
        ;
    }

    private function queryEntradasSaidasDoAno($boEntrada)
    {
        $ano = date('Y');

        return \App\TransactionItem::join('transaction as t', 't.id', '=', 'transaction_item.id_transaction')
            ->whereRaw("YEAR(due_date) = {$ano} and t.is_income = {$boEntrada}")
            ->where('id_user', auth('api')->user()->id)
            ->selectRaw('MONTH(due_date) as mes, COALESCE(sum(transaction_item.value),0) as total')
            ->groupByRaw('MONTH(due_date)')
            ->get()
        ;
    }

    private function graficoEntradasDoAno()
    {
        $entradas = $this->queryEntradasSaidasDoAno(1);
        $grafico = [
            'labels' => ['Jan', 'Fev', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            'datasets' => [[
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'color' => ['rgba(42, 0, 79, 1)`'],
                'strokeWidth' => 2,
            ]],
            'legend' => ['Entradas do ano'],
        ];
        foreach ($entradas as $value) {
            $grafico['datasets'][0]['data'][$value->mes - 1] = (float) $value->total;
        }

        return $grafico;
    }

    private function graficoSaidasDoAno()
    {
        $saidas = $this->queryEntradasSaidasDoAno(0);
        $grafico = [
            'labels' => ['Jan', 'Fev', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            'datasets' => [[
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'color' => ['rgba(42, 0, 79, 1)`'],
                'strokeWidth' => 2,
            ]],
            'legend' => ['Saidas do ano'],
        ];
        foreach ($saidas as $value) {
            $grafico['datasets'][0]['data'][$value->mes - 1] = (float) $value->total;
        }

        return $grafico;
    }
}
