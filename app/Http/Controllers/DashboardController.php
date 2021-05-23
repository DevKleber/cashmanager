<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = date('m');
        $totalEntradas = $this->totalEntradas($currentMonth);
        $totalSaida = $this->totalSaida($currentMonth);
        $totalPlanejamento = ['total' => 89];
        $entradasDoAno = $this->graficoEntradasDoAno();
        $saidasDoAno = $this->graficoSaidasDoAno();

        return [
            'totalEntradas' => $totalEntradas,
            'totalSaida' => $totalSaida,
            'totalPlanejamento' => $totalPlanejamento,
            'totalPlanejamento' => $totalPlanejamento,
            'entradasDoAno' => $entradasDoAno,
            'saidasDoAno' => $saidasDoAno,
        ];
    }

    private function totalEntradas($mes, $ano = null)
    {
        $ano = null === $ano ? date('Y') : $ano;

        return \App\TransactionItem::join('transaction as t', 't.id', '=', 'transaction_item.id_transaction')
            ->whereRaw("MONTH(due_date) = {$mes} AND YEAR(due_date) = {$ano} and t.is_income = true")
            ->where('id_user', auth('api')->user()->id)
            ->selectRaw('sum(transaction_item.value) as total')
            ->first()
        ;
    }

    private function totalSaida($mes, $ano = null)
    {
        $ano = null === $ano ? date('Y') : $ano;

        return \App\TransactionItem::join('transaction as t', 't.id', '=', 'transaction_item.id_transaction')
            ->whereRaw("MONTH(due_date) = {$mes} AND YEAR(due_date) = {$ano} and t.is_income = false")
            ->where('id_user', auth('api')->user()->id)
            ->selectRaw('sum(transaction_item.value) as total')
            ->first()
        ;
    }

    private function queryEntradasSaidasDoAno($boEntrada)
    {
        $ano = date('Y');

        return \App\TransactionItem::join('transaction as t', 't.id', '=', 'transaction_item.id_transaction')
            ->whereRaw("YEAR(due_date) = {$ano} and t.is_income = {$boEntrada}")
            ->where('id_user', auth('api')->user()->id)
            ->selectRaw('MONTH(due_date) as mes, sum(transaction_item.value) as total')
            ->groupByRaw('MONTH(due_date)')
            ->get()
        ;
    }

    private function graficoEntradasDoAno()
    {
        $entradas = $this->queryEntradasSaidasDoAno(1);
        $grafico = [
            'labels' => ['Jan', 'Fev', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            'datasets' => [
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'color' => ['rgba(42, 0, 79, 1)`'],
                'strokeWidth' => 2,
            ],
            'legend' => ['Entradas do ano'],
        ];
        foreach ($entradas as $value) {
            $grafico['datasets']['data'][$value->mes - 1] = (float) $value->total;
        }

        return $grafico;
    }

    private function graficoSaidasDoAno()
    {
        $saidas = $this->queryEntradasSaidasDoAno(0);
        $grafico = [
            'labels' => ['Jan', 'Fev', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            'datasets' => [
                'data' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                'color' => ['rgba(42, 0, 79, 1)`'],
                'strokeWidth' => 2,
            ],
            'legend' => ['Saidas do ano'],
        ];
        foreach ($saidas as $value) {
            $grafico['datasets']['data'][$value->mes - 1] = (float) $value->total;
        }

        return $grafico;
    }
}
