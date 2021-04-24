<?php

namespace App;

use Helpers;
use Illuminate\Database\Eloquent\Model;

class Movimentacao extends Model
{
    protected $table = 'rebanho.movimentacao';
    protected $primaryKey = 'id_movimentacao';
    protected $fillable = ['id_movimentacao', 'dt_movimentacao', 'id_movimentacaotipo', 'id_pessoa', 'bo_ativo', 'bo_fiscal', 'ds_observacao','id_nucleo'];

    public static function getMovimentsByIdCore($id_nucleo)
    {
        $moviments = self::getIdsMovimentsByIdCore($id_nucleo);

        $query = \App\Movimentacao::Join('rebanho.movimentacao_tipo as mt', 'movimentacao.id_movimentacaotipo', '=', 'mt.id_movimentacaotipo')
            ->Join('pessoa.pessoa as p', 'p.id_pessoa', '=', 'movimentacao.id_pessoa')
            ->whereIn('id_movimentacao', $moviments)
            ->select('movimentacao.*', 'mt.no_movimentacaotipo','mt.bo_entrada', 'p.no_pessoa')

        ;
        $whereJoin = ['p.ds_usuario', 'p.no_pessoa', 'mt.no_movimentacaotipo'];
        $query = Filter::searchWhere($query, __CLASS__, $whereJoin);
        $query = Filter::orderBy($query);

        return Filter::paginate($query);
    }

    public static function lancarRebanhoManual($nucleo, $movimentacao, $value, $estratificacoes)
    {
        $estratificacao = $estratificacoes[$value['id_estratificacao']];
        $idadeMedia = intval(($estratificacao->nu_idade_minima + $estratificacao->nu_idade_maxima) / 2);
        $dataNascimento = Helpers::getDataNascimentoByMeses($idadeMedia);
        $totalDeCabecasLancadasIndividual = Helpers::count($value['rebanho']);
        $quantidadeDeclaracadas = $value['quantidade'];
        $totalGerarRebanho = ($quantidadeDeclaracadas - $totalDeCabecasLancadasIndividual);
        if (0 == $totalGerarRebanho) {
            return true;
        }
        $arRebanho['id_nucleo'] = $nucleo->id_nucleo;
        $arRebanho['id_raca'] = \App\Raca::getRacaPadrao();
        $arRebanho['nu_identificacao'] = null;
        $arRebanho['dt_nascimento'] = $dataNascimento;
        $arRebanho['bo_macho'] = $estratificacao['bo_macho'];
        $arRebanho['bo_fiscal'] = $movimentacao->bo_fiscal;

        for ($i = 0; $i < $totalGerarRebanho; ++$i) {
            $rebanho = \App\Rebanho::create($arRebanho);
            if (!$rebanho) {
                \DB::rollBack();

                return false;
            }
            $arRebanho['id_rebanho'] = $rebanho->id_rebanho;
            $movimentacaoFoiSalva = self::lancarMovimentacaoRebanho($movimentacao, $arRebanho);
            if (!$movimentacaoFoiSalva) {
                return false;
            }
            unset($arRebanho['id_rebanho']);
        }

        return true;
    }

    public static function lancarRebanhoDeclarado($nucleo, $movimentacao, $request, $value, $estratificacoes = null)
    {
        foreach ($value['rebanho'] as $keyRebanho => $valueRebanho) {
            $valueRebanho['id_movimentacaotipo'] = $request['id_movimentacaotipo'];
            $valueRebanho['id_nucleo'] = $nucleo->id_nucleo;
            $valueRebanho['bo_fiscal'] = $movimentacao->bo_fiscal;
            $valueRebanho['bo_macho'] = $valueRebanho['bo_macho'] ?? false;
            $valueRebanho['dt_nascimento'] = Helpers::getDataNascimentoByMeses($valueRebanho['meses']);

            $rebanho = \App\Rebanho::create($valueRebanho);
            if (!$rebanho) {
                \DB::rollBack();

                return false;
            }
            $valueRebanho['id_rebanho'] = $rebanho->id_rebanho;
            $movimentacaoFoiSalva = self::lancarMovimentacaoRebanho($movimentacao, $valueRebanho);
            if (!$movimentacaoFoiSalva) {
                return false;
            }
        }

        return true;
    }

    //saída rebanho
    public static function saidaRebanhoManual($request, $movimentacao, $value, $estratificacoes)
    {
        $estratificacao = $estratificacoes[$value['id_estratificacao']];
        $totalDeCabecasLancadasIndividual = Helpers::count($value['rebanho']);
        $quantidadeDeclaracadas = $value['quantidade'];
        $totalGerarRebanho = ($quantidadeDeclaracadas - $totalDeCabecasLancadasIndividual);
        if ($totalGerarRebanho <= 0) {
            return true;
        }
        $arRebanho['id_nucleo'] = $request['id_nucleo'];

        $query = \App\Rebanho::where('id_nucleo', $request['id_nucleo'])
            ->where('bo_vivo', true)
            ->where('bo_fiscal', $request['bo_fiscal'])
            ->where('bo_macho', $estratificacao->bo_macho)
        ;
        if (null != $estratificacao->nu_idade_minima || null != $estratificacao->nu_idade_maxima) {
            $query->whereRaw("
                ((DATE_PART('year', AGE(now(), dt_nascimento))*12) + DATE_PART('month', AGE(now(), dt_nascimento))) BETWEEN {$estratificacao->nu_idade_minima} and {$estratificacao->nu_idade_maxima}
            ");
        }
        $query->limit($totalGerarRebanho);

        $rebanho = $query;
        $rebanhosRegistrados = $rebanho->get();
        if ($rebanho->count() < $totalGerarRebanho) {
            \DB::rollBack();

            throw new \Exception('Quantidade de animais informada é maior do que o saldo do nucleo');

            return false;
        }
        $saidaRebanho = $rebanho->update(['bo_vivo' => false]);

        if (!$saidaRebanho) {
            \DB::rollBack();

            throw new \Exception('Erro ao salvar rebanho');

            return false;
        }
        foreach ($rebanhosRegistrados as $key => $value) {
            $movimentacaoFoiSalva = self::lancarMovimentacaoRebanho($movimentacao, $value->toArray());
            if (!$movimentacaoFoiSalva) {
                throw new \Exception('Erro ao salvar rebanho');

                return false;
            }
        }

        return true;
    }

    public static function saidaRebanhoDeclarado($id_nucleo, $movimentacao, $request, $value, $estratificacoes = null)
    {
        if(!isset($request['bo_fiscal'])){
            throw new \Exception('Informe se essa movimentação é ou não oficial');
        }

        foreach ($value['rebanho'] as $keyRebanho => $valueRebanho) {
            $valueRebanho['id_movimentacaotipo'] = $request['id_movimentacaotipo'];
            $valueRebanho['id_nucleo'] = $id_nucleo;
            $valueRebanho['bo_fiscal'] = $request['bo_fiscal'] ?? false;


            // $rebanho = \App\Rebanho::where('id_rebanho', $valueRebanho['id_rebanho'])
            //     ->where('bo_fiscal', $request['bo_fiscal'])
            //     ->update(['bo_vivo' => 0])
            // ;

            $rebanho = \App\Rebanho::where('id_rebanho', $valueRebanho['id_rebanho'])
                ->update(['bo_vivo' => 0])
            ;
            if (!$rebanho) {
                \DB::rollBack();

                throw new \Exception('Não encontramos em nossa base. Nome:'.$valueRebanho['no_identificacao'].' Número: '.$valueRebanho['nu_identificacao']);

                return false;
            }
            $movimentacaoFoiSalva = self::lancarMovimentacaoRebanho($movimentacao, $valueRebanho);
            if (!$movimentacaoFoiSalva) {
                throw new \Exception('Erro ao salvar rebanho');

                return false;
            }
        }

        return true;
    }

    //fim saída rebanho

    public static function lancarMovimentacaoRebanho($movimentacao, $valueRebanho)
    {
        $valueRebanho['id_movimentacao'] = $movimentacao->id_movimentacao;
        $movimentacaoRebanho = \App\MovimentacaoRebanho::create($valueRebanho);
        if (!$movimentacaoRebanho) {
            \DB::rollBack();

            return false;
        }

        return true;
    }

    public static function checkIfPropertyAlreadyExistsBySpecie($id_especie, $id_propriedade)
    {
        $nucleoByPropriedade = \App\Nucleo::where('id_propriedade', $id_propriedade)->where('id_especie', $id_especie)->first();

        return null != $nucleoByPropriedade ? true : false;
    }

    public static function checkIfNameCoreAlreadyExistsBySpecie($id_especie, $id_propriedade,$ds_nucleo)
    {
        $nucleoByPropriedade = \App\Nucleo::where('id_propriedade', $id_propriedade)
            ->where('id_especie', $id_especie)
            ->where('ds_nucleo', $ds_nucleo)
            ->first()
        ;

        return null != $nucleoByPropriedade ? true : false;
    }

    public static function lancarRebanhoCompleto($value)
    {
        return Helpers::count($value['rebanho']) > 0 ? true : false;
    }

    public static function detail($id)
    {
        $movimentacao = \App\Movimentacao::where('id_movimentacao', $id)
            ->Join('rebanho.movimentacao_tipo as mt', 'movimentacao.id_movimentacaotipo', '=', 'mt.id_movimentacaotipo')
            ->first()
        ;
        if (!$movimentacao) {
            return response(['response' => 'Não existe movimentaçã'], 400);
        }
        $anexos = \App\MovimentacaoArquivo::join('sistema.arquivo as ar', 'ar.id_arquivo', '=', 'movimentacao_arquivo.id_arquivo')
            ->join('rebanho.movimentacao_tipo_arquivo as mta', 'mta.id_movimentacaotipoarquivo', '=', 'movimentacao_arquivo.id_movimentacaotipoarquivo')
            ->where('id_movimentacao', $id)
            ->get()
            ;

        $responsavel = \App\Pessoa::find($movimentacao->id_pessoa);

        $animais = \App\Movimentacao::Join('rebanho.movimentacao_rebanho as mr', 'movimentacao.id_movimentacao', '=', 'mr.id_movimentacao')
            ->Join('rebanho.rebanho as r', 'r.id_rebanho', '=', 'mr.id_rebanho')
            ->Join('rebanho.raca as ra', 'ra.id_raca', '=', 'r.id_raca')
            ->Where('movimentacao.id_movimentacao', $movimentacao->id_movimentacao)
            ->selectRaw("*,(DATE_PART('month', AGE(now(), dt_nascimento))) as mes, DATE_PART('year', AGE(now(), dt_nascimento)) as ano")
            ->get()
        ;
        $id_nucleo = $animais[0]->id_nucleo;
        $propriedade = \App\Nucleo::where('id_nucleo', $id_nucleo)
            ->Join('rebanho.especie as e', 'e.id_especie', '=', 'nucleo.id_especie')
            ->Join('propriedade.propriedade as p', 'p.id_propriedade', '=', 'nucleo.id_propriedade')
            ->first()
        ;

        return [
            'propriedade' => $propriedade,
            'movimentacao' => $movimentacao,
            'anexos' => $anexos,
            'responsavel' => $responsavel,
            'animais' => $animais,
        ];
    }

    private static function getIdsMovimentsByIdCore($id_nucleo)
    {
        $moviments = \App\Movimentacao::Join('rebanho.movimentacao_rebanho as mr', 'movimentacao.id_movimentacao', '=', 'mr.id_movimentacao')
            ->Join('rebanho.rebanho as r', 'r.id_rebanho', '=', 'mr.id_rebanho')
            ->Where('movimentacao.id_nucleo', $id_nucleo)
            ->groupBy('movimentacao.id_movimentacao')
            ->select('movimentacao.id_movimentacao')
            ->get()
        ;
        $ar = [];
        foreach ($moviments as $key => $value) {
            $ar[] = $value->id_movimentacao;
        }

        return $ar;
    }
}
