<?php

namespace App;

use Helpers;
use Illuminate\Database\Eloquent\Model;

class PessoaDocumento extends Model
{
    protected $table = 'pessoa.pessoa_documento as pd';
    protected $primaryKey = 'id_pessoadocumento';
    protected $fillable = ['id_pessoadocumento', 'id_pessoa', 'id_documento', 'created_at', 'updated_at'];

    public static function getDocumentsByIdPessoa($id_pessoa)
    {
        return self::where('id_pessoa', $id_pessoa)
            ->join('pessoa.documento as d', 'd.id_documento', '=', 'pd.id_documento')
            ->join('pessoa.tipo_documento as td', 'td.id_tipodocumento', '=', 'd.id_tipodocumento')
            ->join('pessoa.orgao_expedidor AS oe', 'oe.id_orgaoexpedidor', '=', 'd.id_orgaoexpedidor')
            ->select('pd.*', 'd.*', 'td.no_tipodocumento', 'oe.no_orgaoexpedidor')
            ->get()
        ;
    }

    public static function insertDocument($request, $id_pessoa)
    {
        if (2 == $request['id_tipodocumento']) {
            if (!Helpers::validarCpf($request['nu_documento'])) {
                return response(['response' => 'CPF Inválido'], 400);
            }
        }

        $request['doc']['id_pessoa'] = $id_pessoa;
        $request['doc']['id_orgaoexpedidor'] = $request['id_orgaoexpedidor'];
        $request['doc']['id_tipodocumento'] = $request['id_tipodocumento'];
        $request['doc']['nu_documento'] = Helpers::removerCaracteresEspeciaisEspacos($request['nu_documento']);

        return \App\Documento::create($request['doc']);
    }

    public static function insertPersonDocument($id_pessoa, $id_documento)
    {
        $value['doc']['id_pessoa'] = $id_pessoa;
        $value['doc']['id_documento'] = $id_documento;

        return self::create($value['doc']);
    }

    public static function insertDocumentsByIdPerson($request, $id_pessoa)
    {
        foreach ($request as $key => $value) {
            $value['id_pessoa'] = $id_pessoa;
            $document = static::insertDocument($value, $id_pessoa);
            if (!isset($document->id_documento)) {
                return $document;
            }
            $personDocument = static::insertPersonDocument($id_pessoa, $document->id_documento);
            if (!$document || !$personDocument) {
                return  response(['response' => 'Erro ao salvar ProdutoCardapio'], 400);
            }
        }

        return true;
    }

    public static function updateDocumentsByidPerson($request, $id_pessoa)
    {
        $documentsPerson = self::where('id_pessoa', $id_pessoa)->get();
        foreach ($documentsPerson as $key => $value) {
            self::where('id_pessoadocumento', $value['id_pessoadocumento'])->delete();
            \App\Documento::where('id_documento', $value['id_documento'])->delete();
        }

        return self::insertDocumentsByIdPerson($request->documents, $id_pessoa);
    }
}
