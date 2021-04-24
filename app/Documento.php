<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'pessoa.documento as d';
    protected $primaryKey = 'id_documento';
    protected $fillable = ['id_documento', 'id_orgaoexpedidor', 'id_tipodocumento', 'nu_documento', 'created_at', 'updated_at'];

    public static function getCpfInArray($dados)
    {
        $cpfFilter =
        array_filter(
            $dados,
            function ($doc) {
                return 2 == $doc['id_tipodocumento'];
            }
        );

        return current($cpfFilter)['nu_documento'] ?? null;
    }

    public static function cpfExists($cpf, $idPessoaAltered = false)
    {
        $clientes = self::join('pessoa.pessoa_documento as pd', 'pd.id_documento', '=', 'd.id_documento')
            ->where('nu_documento', $cpf)
            ->first()
        ;
        if (!$clientes) {
            return false;
        }

        if ($idPessoaAltered) {
            if ($clientes->id_pessoa == $idPessoaAltered) {
                return false;
            }

            return true;
        }

        return true;
    }
}
