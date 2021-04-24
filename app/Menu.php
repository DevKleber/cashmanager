<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'sistema.rotina as r';
    protected $primaryKey = 'id_rotina';
    protected $fillable = ['id_rotina', 'id_modulo', 'id_rotinatipo', 'no_rotina', 'ds_rotina', 'no_abreviatura', 'bo_publica', 'nu_ordem', 'no_icone', 'bo_ativo'];

    public static function getRotinas()
    {
        return self::join('sistema.rotina_tipo as rt', 'rt.id_rotinatipo', '=', 'r.id_rotinatipo')
            ->where('r.bo_ativo', true)
            ->orderBy('r.nu_ordem')
            ->select('r.*', 'rt.no_rotinatipo')
            ->get()
        ;
    }

    public static function getFuncionalidades()
    {
        $funcionalidades = \DB::table('sistema.funcionalidade as f')
            ->Join('sistema.rotina as r', 'r.id_rotina', '=', 'f.id_rotina')
            ->Join('sistema.funcionalidade_tipo as ft', 'ft.id_funcionalidadetipo', '=', 'f.id_funcionalidadetipo')
            ->select('f.id_funcionalidade', 'f.bo_ativo', 'f.id_rotina', 'ft.no_funcionalidadetipo', 'ft.icon')
            ->where('f.bo_ativo', true)
            ->get()
        ;
        $ar = [];
        foreach ($funcionalidades as $key => $value) {
            $ar[$value->id_rotina][] = $value;
        }

        return $ar;
    }

    public static function menu()
    {
        $rotinas = self::getRotinas();
        $funcionalidades = self::getFuncionalidades();
        $menu = [];
        foreach ($rotinas as $key => $value) {
            $menu[$key] = $value;
            $menu[$key]['funcionalidades'] = $funcionalidades[$value->id_rotina] ?? [];
        }

        return $rotinas;
    }
}
