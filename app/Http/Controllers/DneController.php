<?php

namespace App\Http\Controllers;

class DneController extends Controller
{
    public function getStates()
    {
        $dne = \DB::table('pessoa.estado')->get();
        if (!$dne) {
            return response(['response' => 'Estados não existe '], 400);
        }

        return response(['dados' => $dne]);
    }

    public function getCities($id)
    {
        $dne = \App\Dne::where('id_estado', $id)->get();
        if (!$dne) {
            return response(['response' => 'Não existe Dne'], 400);
        }

        return response(['dados' => $dne]);
    }

    public function getCity($id)
    {
        $dne = \App\Dne::find($id);
        if (!$dne) {
            return response(['response' => 'Não existe Dne'], 400);
        }

        return response($dne);
    }
}
