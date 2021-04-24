<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Propriedade extends Model
{
    protected $table = 'propriedade.propriedade';
    protected $primaryKey = 'id_propriedade';
    protected $fillable = ['id_propriedade', 'id_empresa', 'id_localidade', 'no_propriedade', 'nu_inscricaoestadual', 'vl_area', 'ds_roteiroacesso', 'bo_ativo', 'nu_latitude', 'nu_longitude', 'bo_cadastro_completo'];

    public static function getProperty($id_propriedade = null)
    {
        $property = self::join('pessoa.empresa as ep', 'ep.id_empresa', '=', 'propriedade.propriedade.id_empresa')
            ->join('pessoa.localidade as l', 'l.id_localidade', '=', 'propriedade.propriedade.id_localidade')
            ->join('pessoa.estado as e', 'e.id_estado', '=', 'l.id_estado')
            ->where('id_propriedade', $id_propriedade)
            ->first()
        ;
        $galeria = \App\PropriedadeGaleria::join('sistema.arquivo', 'sistema.arquivo.id_arquivo', '=', 'propriedade.galeria.id_arquivo')->where('bo_ativo', true)->where('id_propriedade', $property->id_propriedade)->orderBy('id_galeria', 'desc')->get();

        return [
            'property' => $property,
            'galeria' => $galeria,
        ];
    }

    public static function getAllPropertyActive()
    {
        return self::getAllProperty(['propriedade.propriedade.bo_ativo' => true]);
    }

    public static function getAllPropertyInactive()
    {
        return self::getAllProperty(['propriedade.propriedade.bo_ativo' => false]);
    }

    public static function getAllProperty($coluns = [])
    {
        $query = self::join('pessoa.empresa as ep', 'ep.id_empresa', '=', 'propriedade.propriedade.id_empresa')
            ->join('pessoa.localidade as l', 'l.id_localidade', '=', 'propriedade.propriedade.id_localidade')
            ->join('pessoa.estado as e', 'e.id_estado', '=', 'l.id_estado')
            ->select('propriedade.propriedade.*', 'l.no_localidade', 'e.no_estado', 'e.sg_estado', 'e.id_estado')
        ;

        foreach ($coluns as $key => $value) {
            $query->where("{$key}", $value);
        }

        $whereJoin = ['l.no_localidade'];
        $query = Filter::searchWhere($query, __CLASS__, $whereJoin);
        $query = Filter::orderBy($query);

        return Filter::paginate($query);
    }

    public static function IeExist($ie, $idPropertyAltered = false)
    {
        $ieProperty = self::where('nu_inscricaoestadual', "{$ie}")
            ->first()
        ;
        if (!$ieProperty) {
            return false;
        }

        if ($idPropertyAltered) {
            if ($ieProperty->id_propriedade == $idPropertyAltered) {
                return false;
            }

            return true;
        }

        return true;
    }
}
