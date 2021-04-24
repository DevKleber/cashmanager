<?php

namespace App\Http\Controllers;

class DeclaracaoAgenciaAgropecuariaController extends Controller
{
    public function index($id)
    {
        // 'orcamentoItens' => null,
        // 'empresa' => null,
        // 'cliente' => null,
        $pdf = \App::make('dompdf.wrapper')->loadView(
            'declaracao',
            [
                'empresa' => $this->getCompany(),
                'propriedade' => $this->getProperty($id),
            ]
        );
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        \Storage::put('temp/declaracao/propriedade.pdf', $pdf->output());

        return $pdf->stream('propriedade.pdf');
    }

    public function getCompany()
    {
        return \App\Empresa::first();
    }

    public function getProperty($id)
    {
        return \App\Propriedade::getProperty($id)['property'];
    }
}
