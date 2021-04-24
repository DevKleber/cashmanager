<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Helpers;
use JWTAuth;

class EstratificacaoController extends Controller
{
    private $token;
    public function __construct()
    {
        $this->token = JWTAuth::parseToken()->authenticate();
    }
    
    public function index()
    {
        $Estratificacao = \App\Estratificacao::all();
        if(!$Estratificacao){
            return response(["response"=>"Não existe Estratificacao"],400);
        }
        return response(["dados"=>$Estratificacao]);
    }

    
    public function store(Request $request)
    {
        
        $request['bo_ativo'] = true;
        
        $Estratificacao = \App\Estratificacao::create($request->all());
        if(!$Estratificacao){
            return  response(["response"=>"Erro ao salvar Estratificacao"],400); 
        }
        return response(["response"=>"Salvo com sucesso",'dados'=>$Estratificacao]);
        
    }

    
    public function show($id)
    {
        $Estratificacao =\App\Estratificacao::find($id);
        if(!$Estratificacao){
            return response(["response"=>"Não existe Estratificacao"],400);
        }
        return response($Estratificacao);
    }

    
    public function update(Request $request, $id)
    {
        $Estratificacao =  \App\Estratificacao::find($id);
        
        if(!$Estratificacao){
            return response(['response'=>'Estratificacao Não encontrado'],400);
        }
        $Estratificacao = Helpers::processarColunasUpdate($Estratificacao,$request->all());
        
        if(!$Estratificacao->update()){
            return response(['response'=>'Erro ao alterar'],400);
        }
        return response(['response'=>'Atualizado com sucesso']);
      
    }
    

    public function destroy($id)
    {
        $Estratificacao =  \App\Estratificacao::find($id);
        
        if(!$Estratificacao){
            return response(['response'=>'Estratificacao Não encontrado'],400);
        }
        $Estratificacao->bo_ativo = false;
        if(!$Estratificacao->save()){
            return response(["response"=>"Erro ao deletar Estratificacao"],400);
        }
        return response(['response'=>'Estratificacao Inativado com sucesso']);
    }
}