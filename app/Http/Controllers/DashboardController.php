<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEntradas = $this->totalEntradas(4);
        $totalSaida = $this->totalSaida(4);
        $totalPlanejamento = ["total"=>89];

        return [
            'totalEntradas' => $totalEntradas,
            'totalSaida' => $totalSaida,
            'totalPlanejamento' => $totalPlanejamento,
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
}
