<?php

namespace App;

use Helpers;
use Illuminate\Database\Eloquent\Model;

class PessoaTelefone extends Model
{
    protected $table = 'pessoa.pessoa_telefone as pt';
    protected $primaryKey = 'id_pessoatelefone';
    protected $fillable = ['id_pessoatelefone', 'id_telefone', 'id_pessoa', 'created_at', 'updated_at'];

    public static function getPhoneNumbersByIdPessoa($id_pessoa)
    {
        return self::where('id_pessoa', $id_pessoa)
            ->join('pessoa.telefone as t', 't.id_telefone', '=', 'pt.id_telefone')
            ->join('pessoa.tipo_telefone as tt', 'tt.id_tipotelefone', '=', 't.id_tipotelefone')
            ->get()
        ;
    }

    public static function updatePhoneNumbersByidPerson($request, $id_pessoa)
    {
        $addressesPerson = self::where('id_pessoa', $id_pessoa)->get();
        foreach ($addressesPerson as $key => $value) {
            self::where('id_pessoatelefone', $value['id_pessoatelefone'])->delete();
            \App\Telefone::where('id_telefone', $value['id_telefone'])->delete();
        }

        return self::insertPhoneNumbersByIdPerson($request->phoneNumbers, $id_pessoa);
    }

    public static function insertPhoneNumbersByIdPerson($request, $id_pessoa)
    {
        foreach ($request as $key => $value) {
            $address = self::insertPhoneNumber($value);
            $personAddress = self::insertPersonPhoneNumbers($id_pessoa, $address->id_telefone);
            if (!$address || !$personAddress) {
                return  response(['response' => 'Erro ao salvar ProdutoCardapio'], 400);
            }
        }

        return true;
    }

    public static function insertPhoneNumber($request)
    {
        $dados['id_tipotelefone'] = $request['id_tipotelefone'];
        $dados['nr_telefone'] = Helpers::removerCaracteresEspeciaisEspacos($request['nr_telefone']);

        return \App\Telefone::create($dados);
    }

    public static function insertPersonPhoneNumbers($id_pessoa, $id_telefone)
    {
        $dados['id_pessoa'] = $id_pessoa;
        $dados['id_telefone'] = $id_telefone;

        return self::create($dados);
    }
}
