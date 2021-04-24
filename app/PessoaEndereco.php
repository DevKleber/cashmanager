<?php

namespace App;

use Helpers;
use Illuminate\Database\Eloquent\Model;

class PessoaEndereco extends Model
{
    protected $table = 'pessoa.pessoa_endereco as pessoa_endereco';
    protected $primaryKey = 'id_pessoaendereco';
    protected $fillable = ['id_pessoaendereco', 'id_pessoa', 'id_endereco', 'created_at', 'updated_at'];

    public static function getAddressesByIdPessoa($id_pessoa)
    {
        return self::where('id_pessoa', $id_pessoa)
            ->join('pessoa.endereco as endereco', 'endereco.id_endereco', '=', 'pessoa_endereco.id_endereco')
            ->join('pessoa.tipo_endereco as tp_endereco', 'tp_endereco.id_tipoendereco', '=', 'endereco.id_tipoendereco')
            ->join('pessoa.localidade as loc', 'loc.id_localidade', '=', 'endereco.id_localidade')
            ->join('pessoa.estado as uf', 'uf.id_estado', '=', 'loc.id_estado')
            ->select('pessoa_endereco.*', 'endereco.*', 'loc.id_estado', 'loc.no_localidade', 'loc.id_localidade_pai', 'loc.bo_municipio', 'tp_endereco.no_tipoendereco', 'uf.no_estado', 'uf.sg_estado')
            ->get()
        ;
    }

    public static function updateAddressesByidPerson($request, $id_pessoa)
    {
        $addressesPerson = self::where('id_pessoa', $id_pessoa)->get();
        foreach ($addressesPerson as $key => $value) {
            self::where('id_pessoaendereco', $value['id_pessoaendereco'])->delete();
            \App\Endereco::where('id_endereco', $value['id_endereco'])->delete();
        }

        return self::insertAddressesByIdPerson($request->addresses, $id_pessoa);
    }

    public static function insertAddressesByIdPerson($request, $id_pessoa)
    {
        foreach ($request as $key => $value) {
            $address = self::insertAddress($value);
            $personAddress = self::insertPersonAddress($id_pessoa, $address->id_endereco);
            if (!$address || !$personAddress) {
                return  response(['response' => 'Erro ao salvar ProdutoCardapio'], 400);
            }
        }

        return true;
    }

    public static function insertAddress($request)
    {
        $dados['id_tipoendereco'] = $request['id_tipoendereco'];
        $dados['id_localidade'] = $request['id_localidade'];
        $dados['ds_endereco'] = $request['ds_endereco'];
        $dados['ds_complemento'] = $request['ds_complemento'];
        $dados['nr_cep'] = Helpers::removerCaracteresEspeciaisEspacos($request['nr_cep']);
        $dados['nu_endereco'] = $request['nu_endereco'];

        return \App\Endereco::create($dados);
    }

    public static function insertPersonAddress($id_pessoa, $id_endereco)
    {
        $dados['id_pessoa'] = $id_pessoa;
        $dados['id_endereco'] = $id_endereco;

        return self::create($dados);
    }
}
