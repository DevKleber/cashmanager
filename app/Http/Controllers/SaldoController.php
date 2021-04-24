<?php

namespace App\Http\Controllers;

class SaldoController extends Controller
{
    public function getSaldoByNucleo($id)
    {
        return response(['dados' => \App\Saldo::getSaldoByNucleo($id)]);
    }

    public function getRebanhoByNucleo($id)
    {
        return response(['dados' => \App\Saldo::getRebanhoByNucleo($id)]);
    }
    public function getRebanhoComIdentificacaoByNucleo($id)
    {
        return response(['dados' => \App\Saldo::getRebanhoComIdentificacaoByNucleo($id)]);
    }
}
